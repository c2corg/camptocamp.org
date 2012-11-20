require "base64"
require "md5"

module Sass::Script::Functions

  # Given the (css) path to a file, returns it prefixed with the 'hash'
  # (which is in fact the 8 first chars of mda5, which is shorter and
  # provides enough differentiation between two versions of a file)
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

  # Returns a new list after removing any non-true values
  def compact(*args)
    sep = :comma
    if args.size == 1 && args.first.is_a?(Sass::Script::List)
      list = args.first
      args = list.value
      sep = list.separator
    end
    Sass::Script::List.new(args.reject{|a| !a.to_bool}, sep)
  end

  # Returns the size of the list.
  def _compass_list_size(list)
    assert_list list
    Sass::Script::Number.new(list.value.size)
  end

  def assert_list(value)
    unless value.is_a?(Sass::Script::List)
      raise ArgumentError.new("#{value.inspect} is not a list")
    end
  end

  declare :c2chash, :args => [:file]
  declare :datauri, :args => [:file]

end
