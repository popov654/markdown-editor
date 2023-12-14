for file in uploads/*; do
    filename=$(basename "$file" | cut -d. -f1)
    cwebp -q 80 "$file" -o "images/$filename.webp";
    old_path=$(echo $file | sed 's/\//\\\//g');
    sleep 1;
    # command="Command is s/$old_path/images\/$filename.webp/g";
    for article in articles/*; do
        sed -i "s/$old_path/images\/$filename.webp/g" $article
    done
done