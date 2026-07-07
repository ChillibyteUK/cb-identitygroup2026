// generate-theme-json.js
// Node.js script to parse SCSS tokens and generate theme.json for WordPress theme

const fs = require('fs');
const path = require('path');

const scssFile = path.join(__dirname, 'src/sass/theme/_tokens.scss');
const themeJsonFile = path.join(__dirname, 'theme.json');


// Parse CSS custom properties from :root block
function parseCssVariables(scssContent) {
  const rootBlockMatch = scssContent.match(/:root\s*{([\s\S]*?)}/);
  if (!rootBlockMatch) return {};
  const rootContent = rootBlockMatch[1];
  const varRegex = /--([\w-]+):\s*([^;]+);/g;
  const tokens = {};
  let match;
  while ((match = varRegex.exec(rootContent)) !== null) {
    tokens[match[1]] = match[2].trim();
  }
  return tokens;
}

// Map CSS variables to theme.json structure

function buildThemeJson(tokens) {
  // Helper to resolve hsl(var(--col-*-hsl)) to actual hsl value
  function resolveColorValue(value, key) {
    const hslVarMatch = value.match(/^hsl\(var\(--([\w-]+-hsl)\)\)$/);
    if (hslVarMatch) {
      const hslKey = hslVarMatch[1];
      if (tokens[hslKey]) {
        return `hsl(${tokens[hslKey]})`;
      }
    }
    return value;
  }

  // Map color variables (starts with col-, but not -hsl)
  const colors = Object.entries(tokens)
    .filter(([key]) => key.startsWith('col-') && !key.endsWith('-hsl'))
    .map(([key, value]) => ({
      name: key.replace('col-', ''),
      slug: key.replace('col-', ''),
      color: resolveColorValue(value, key)
    }));

  // Map font size variables (starts with fs-)
  const fontSizes = Object.entries(tokens)
    .filter(([key]) => key.startsWith('fs-'))
    .map(([key, value]) => ({
      name: key.replace('fs-', ''),
      slug: key.replace('fs-', ''),
      size: value
    }));

  return {
    version: 2,
    settings: {
      spacing: {
        blockGap: true,
        margin: true,
        padding: true,
        units: ['px', 'em', 'rem', 'vh', 'vw', '%']
      },
      color: { 
        palette: colors.map(color => ({
          ...color,
          origin: 'theme'
        }))
      },
      typography: { fontSizes: fontSizes }
    },
    styles: {
      spacing: {
        blockGap: '28px'
      }
    }
  };
}

function main() {
  if (!fs.existsSync(scssFile)) {
    console.error('SCSS tokens file not found:', scssFile);
    process.exit(1);
  }
  const scssContent = fs.readFileSync(scssFile, 'utf8');
  const tokens = parseCssVariables(scssContent);
  const themeJson = buildThemeJson(tokens);
  fs.writeFileSync(themeJsonFile, JSON.stringify(themeJson, null, 2));
  console.log('theme.json generated successfully.');
}

main();
