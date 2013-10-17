# shortcut to build css file with sass

set -e # exit on error

# delete existing css files, prevents keeping obsolete ones forever
rm *.css

for i in `ls sass/*.scss | cut -c6- | grep -v '^_'`; do
  sass --unix-newlines -t expanded -r c2c_sass_functions.rb sass/$i ${i%scss}css --trace
  echo ${i%scss}css has been updated
done
