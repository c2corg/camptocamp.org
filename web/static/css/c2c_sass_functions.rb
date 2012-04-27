require "base64"
require "md5"

module Sass::Script::Functions

  # Given the (css) path to a file, returns it prefixed with the 'hash'
  # (which is in fact the 8 first chars of mda5, which is shorter and
  # provides enough differentiation between two versions of a file
  def c2chash(file)
    assert_type file, :String
    ffile = "../.." + file.value
    timestamp = MD5.new(File.read(ffile))
    Sass::Script::String.new("/" + timestamp.to_s[0,8] + file.value)
  rescue
    raise Sass::SyntaxError.new("File " + file.value + " not found")
  end

  # Given the (css) path to a file, returns it as data:uri
  def datauri(file)
    assert_type file, :String
    ffile = "../.." + file.value
    Sass::Script::String.new("data:image/" + File.extname(ffile)[1..-1] \
      + ";base64," + Base64.encode64(File.read(ffile)).strip.gsub(/\n/,''))
  rescue
     raise Sass::SyntaxError.new("File " + file.value + " not found")
  end

  declare :c2chash, :args => [:file]
  declare :datauri, :args => [:file]

end
