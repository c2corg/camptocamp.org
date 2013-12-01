require "c2c"
require "md5"

css_dir = "../css"
sass_dir = "."
images_dir = "../.."

# output_style = :expanded or :nested or :compact or :compressed
output_style = :expanded

line_comments = false

# append hash before url for versioning
# (which is in fact the 8 first chars of mda5, which is shorter and
# provides enough differentiation between two versions of a file)   
asset_cache_buster do |path, real_path|
  if File.exists?(real_path)
    pathname = Pathname.new(path)
    hash = MD5.file(real_path.path)
    new_path = "/%s%s" % [hash.to_s[0,8], pathname.cleanpath]

    {:path => new_path, :query => nil}
  else
    raise Compass::Error, "File not found: %s" % Pathname.new(real_path.path).cleanpath
  end
end
