#!/bin/bash

for file in uploads/*; do
    filename=$(basename "$file" | cut -d. -f1);
    echo -n "Running convert '$file' -quality 80 'images/$filename.webp'... ";
    convert "$file" -quality 80 "images/$filename.webp";
    echo "done.";
    old_path_enc=$(echo $file | sed 's/\//\\\//g' | sed 's/ /%20/g');
    filename_enc=$(echo $filename | sed 's/ /%20/g');
    sleep 1;
    # command="Command is s/$old_path_enc/images\/$filename_enc.webp/g";
    for article in articles/*; do
        sed -i "s/$old_path_enc/images\/$filename_enc.webp/g" $article;
    done
done