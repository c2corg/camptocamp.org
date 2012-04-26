require "base64"

module Sass::Script::Functions

  # Given the (css) path to a file, returns it prefixed with the timestamp
  def timestamp(file)
    assert_type file, :String

    ffile = "../.." + file.value
    timestamp = File.mtime(ffile).to_i - 1200000000
    Sass::Script::String.new("/" + timestamp.to_s + file.value)
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

  declare :timestamp, :args => [:file]
  declare :datauri, :args => [:file]

end
