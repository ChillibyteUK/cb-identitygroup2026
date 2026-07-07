#!/bin/bash

# Cleanup orphaned block registrations that don't have ACF JSON files

blocks_php="./inc/cb-blocks.php"
acf_json_dir="./acf-json"

if [ ! -f "$blocks_php" ]; then
  echo "Error: $blocks_php not found"
  exit 1
fi

if [ ! -d "$acf_json_dir" ]; then
  echo "Error: $acf_json_dir not found"
  exit 1
fi

# Get list of block slugs from ACF JSON files (group_cb_*.json)
echo "Scanning ACF JSON files..."
mapfile -t acf_blocks < <(ls "$acf_json_dir"/group_cb_*.json 2>/dev/null | xargs -n 1 basename | sed 's/^group_//' | sed 's/\.json$//')

if [ ${#acf_blocks[@]} -eq 0 ]; then
  echo "No ACF JSON files found"
  exit 1
fi

echo "Found ${#acf_blocks[@]} ACF JSON files"
echo ""

# Extract block names from cb-blocks.php
echo "Checking for orphaned blocks in $blocks_php..."
temp_file=$(mktemp)
orphaned_blocks=()

# Parse cb-blocks.php and collect block names
while IFS= read -r line; do
  if [[ "$line" =~ \'name\'[[:space:]]*=\>[[:space:]]*\'([^\']+)\' ]]; then
    block_name="${BASH_REMATCH[1]}"
    
    # Check if this block has an ACF JSON file
    has_json=false
    for acf_block in "${acf_blocks[@]}"; do
      if [ "$acf_block" == "$block_name" ]; then
        has_json=true
        break
      fi
    done
    
    if [ "$has_json" == "false" ]; then
      orphaned_blocks+=("$block_name")
    fi
  fi
done < "$blocks_php"

if [ ${#orphaned_blocks[@]} -eq 0 ]; then
  echo "No orphaned blocks found!"
  exit 0
fi

# Display orphaned blocks
echo "Found ${#orphaned_blocks[@]} orphaned block(s):"
echo ""
for block in "${orphaned_blocks[@]}"; do
  echo "  - $block"
done
echo ""

read -p "Remove these orphaned blocks from $blocks_php? (y/n): " confirm

if [ "$confirm" != "y" ] && [ "$confirm" != "Y" ]; then
  echo "Cancelled."
  exit 0
fi

# Remove orphaned blocks
for block_slug in "${orphaned_blocks[@]}"; do
  echo "Removing: $block_slug"
  
  awk -v block_slug="$block_slug" '
    BEGIN { in_block = 0; block_started = 0 }
    
    /acf_register_block_type\(/ {
      block_started = 1
      block_buffer = $0 "\n"
      next
    }
    
    block_started {
      block_buffer = block_buffer $0 "\n"
      
      # Check if this line contains the block name we want to remove
      if ($0 ~ "'"'"'name'"'"'.*=>.*'"'"'" block_slug "'"'"'") {
        in_block = 1
      }
      
      # Found the end of the block registration
      if ($0 ~ /\);$/) {
        block_started = 0
        if (!in_block) {
          # This is not the block we want to remove, so print it
          printf "%s", block_buffer
        }
        in_block = 0
        block_buffer = ""
        next
      }
      next
    }
    
    { print }
  ' "$blocks_php" > "$temp_file"
  
  if [ -s "$temp_file" ]; then
    mv "$temp_file" "$blocks_php"
  else
    echo "Error processing $block_slug"
    rm "$temp_file"
    exit 1
  fi
done

# Set correct permissions
chmod 664 "$blocks_php"
chgrp www-data "$blocks_php" 2>/dev/null || true

echo ""
echo "Cleanup complete! Removed ${#orphaned_blocks[@]} orphaned block(s)."
