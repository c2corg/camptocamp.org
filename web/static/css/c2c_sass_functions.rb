require "base64"
require "md5"

module Sass::Script::Functions

  # Given the (css) path to a file, returns it prefixed with the 'hash'
  # (which is in fact the 8 first chars of mda5, which is shorter and
  # provides enough differentiation between two versions of a file)
  def c2chash(file, pixelratio = Sass::Script::Number.new(1))
    assert_type file, :String
    assert_type pixelratio, :Number

    ffile = pixelratio_file(file, pixelratio)
    hash = MD5.new(File.read("../.." + ffile))
    Sass::Script::String.new("/" + hash.to_s[0,8] + ffile)
  rescue
    raise Sass::SyntaxError.new("File " + file.value + " not found")
  end

  declare :c2chash, :args => [:file]
  declare :c2chash, :args => [:file, :pixelratio]

  # Given the (css) path to a file, returns it as data:uri
  def datauri(file, pixelratio = Sass::Script::Number.new(1))
    assert_type file, :String
    assert_type pixelratio, :Number

    ffile = pixelratio_file(file, pixelratio)
    Sass::Script::String.new("data:image/" + File.extname(ffile)[1..-1] \
      + ";base64," + Base64.encode64(File.read("../.." + ffile)).strip.gsub(/\n/,''))
  rescue
     raise Sass::SyntaxError.new("File " + file.value + " not found") # not really a syntax error, but...
  end

  declare :datauri, :args => [:file]
  declare :datauri, :args => [:file, :pixelratio]

  # Given a file and a pixel ratio, return best candidate file
  # For example, looking for file img.png with pixelratio 2 will
  # return img2x.png if it exits, img.png otherwise
  # A pixelratio of 1 will end with no suffix appended
  def pixelratio_file(file, pixelratio)
    assert_type file, :String
    assert_type pixelratio, :Number

    filepath = file.value
    if pixelratio.value != 1
      ext = File.extname(file.value)
      basename = File.basename(file.value, ext)
      dir = File.dirname(file.value)
      pixelratio_filepath = dir + "/" + basename + "@" + pixelratio.value.to_s + "x" + ext
      if File.exists?("../.." + pixelratio_filepath)
        filepath = pixelratio_filepath
      else
        Sass::Util.sass_warn("Warning: using default pixel ratio file for " + file.value +
                             " (" + pixelratio.to_s + "x version not available)")
      end
    end
    filepath
  end

  # Given a file, returns its width
  # and height
  def file_dimensions(file)
    assert_type file, :String

    filepath = "../.." + file.value
    size = %x[identify -format "%[width]px %[height]px" #{filepath}]
    Sass::Script::String.new(size.strip)
  end

  declare :file_dimensions, :args => [:file]

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

end
