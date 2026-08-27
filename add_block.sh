#!/bin/bash

include_color=false

while getopts ":c" opt; do
  case "$opt" in
    c)
      include_color=true
      ;;
    \?)
      echo "Invalid option: -$OPTARG"
      echo "Usage: $0 [-c]"
      exit 1
      ;;
  esac
done

shift $((OPTIND - 1))

# Prompt for block name (or use first positional argument)
if [ -n "$1" ]; then
  block_name="$1"
else
  read -p "Enter block name: " block_name
fi

# Exit if empty
if [ -z "$block_name" ]; then
  echo "No block name provided."
  exit 1
fi

# Convert to lowercase and replace spaces
block_slug=$(echo "$block_name" | tr '[:upper:]' '[:lower:]' | tr ' ' '_')
block_kebab=$(echo "$block_name" | tr '[:upper:]' '[:lower:]' | tr ' ' '-')

# Define file paths
php_file="./blocks/${block_kebab}.php"
scss_file="./src/sass/theme/blocks/_${block_slug}.scss"
blocks_scss="./src/sass/theme/blocks/_blocks.scss"
blocks_php="./inc/cb-blocks.php"
acf_json_file="./acf-json/group_${block_slug}.json"

# Exit if block already exists, with specific feedback
if [ -f "$php_file" ]; then
  echo "PHP block file already exists: $php_file"
  exit 1
fi

if [ -f "$scss_file" ]; then
  echo "SCSS block file already exists: $scss_file"
  exit 1
fi

if [ -f "$acf_json_file" ]; then
  echo "ACF JSON file already exists: $acf_json_file"
  exit 1
fi


# Grab package name from style.css
style_file="./style.css"
package=$(grep "Text Domain:" "$style_file" | sed 's/.*Text Domain:[ ]*//')

# Create files
if [ "$include_color" = true ]; then
  cat > "$php_file" <<EOF
<?php
/**
 * Block template for ${block_name}.
 *
 * @package ${package}
 */

defined( 'ABSPATH' ) || exit;

// Support Gutenberg color picker.
\$bg         = ! empty( \$block['backgroundColor'] ) ? 'has-' . \$block['backgroundColor'] . '-background-color' : '';
\$fg         = ! empty( \$block['textColor'] ) ? 'has-' . \$block['textColor'] . '-color' : '';
\$section_id = \$block['anchor'] ?? null;
\$extra      = \$block['className'] ?? 'py-5';

?>
<section class="${block_kebab} <?= esc_attr( trim( \$bg . ' ' . \$fg . ' ' . \$extra ) ); ?>" id="<?= esc_attr( \$section_id ); ?>">

</section>
EOF
else
  cat > "$php_file" <<EOF
<?php
/**
 * Block template for ${block_name}.
 *
 * @package ${package}
 */

defined( 'ABSPATH' ) || exit;
EOF
fi

touch "$scss_file"
echo "Created: $php_file"
echo "Created: $scss_file"

# Append import to _blocks.scss
if ! grep -q "@import '${block_slug}';" "$blocks_scss"; then
  echo "@import '${block_slug}';" >> "$blocks_scss"
  echo "Added @import to $blocks_scss"
else
  echo "Import already exists in $blocks_scss"
fi
# Define the marker comment to look for
marker_comment="// INSERT NEW BLOCKS HERE."

color_support=""
if [ "$include_color" = true ]; then
  color_support=$(cat <<'EOF'
          'color'     => array(
            'background' => true,
            'text'       => true,
          ),
EOF
)
fi

# Insert block registration code at the marker comment
block_code=$(cat <<EOF

		acf_register_block_type(
			array(
				'name'            => '${block_slug}',
				'title'           => __( '${block_name}' ),
				'category'        => 'layout',
				'icon'            => 'cover-image',
				'render_template' => 'blocks/${block_kebab}.php',
				'mode'            => 'edit',
				'supports'        => array(
					'mode'      => false,
					'anchor'    => true,
					'className' => true,
					'align'     => true,
          ${color_support}
				),
			)
		);
EOF
)

# Check if the marker comment exists in the file
if ! grep -q "$marker_comment" "$blocks_php"; then
  echo "Marker comment not found in $blocks_php. Please add the following comment to the file:"
  echo "$marker_comment"
  exit 1
fi

# Insert block registration code at the marker comment
temp_file=$(mktemp) # Create a temporary file
if awk -v block_code="$block_code" -v marker="$marker_comment" '
    $0 ~ marker { print; print block_code; next }
    { print }
' "$blocks_php" > "$temp_file"; then
  mv "$temp_file" "$blocks_php"
  chmod 664 "$blocks_php" # Set correct permissions
  chgrp www-data "$blocks_php" # Set correct group ownership
  echo "Block registered in $blocks_php"
else
  echo "Failed to insert block registration code into $blocks_php"
  rm "$temp_file"
  exit 1
fi

# Create ACF JSON
acf_json_content="{
  \"key\": \"group_${block_slug}\",
  \"title\": \"${block_name}\",
  \"fields\": [
    {
      \"key\": \"field_${block_slug}_title\",
      \"label\": \"${block_name}\",
      \"name\": \"title\",
      \"type\": \"message\"
    }
  ],
  \"location\": [
    [
      {
        \"param\": \"block\",
        \"operator\": \"==\",
        \"value\": \"acf/${block_kebab}\"
      }
    ]
  ],
  \"active\": 1,
  \"description\": \"\"
}"
echo "$acf_json_content" > "$acf_json_file"
echo "Created ACF field group JSON: $acf_json_file"