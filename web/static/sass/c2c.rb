require "sass"

module Sass::Script::Functions

  # given a relative path to an image and a target ratio
  # check if the corresponding file exists, and return it,
  # else return the default one
  def pixelratio_file(file, pixelratio)
    assert_type file, :String
    assert_type pixelratio, :Number

    path = file.value
    px = pixelratio.value

    if px != 1
      # Compute the real path to the image on the file stystem if the images_dir is set
      real_path = if Compass.configuration.images_dir
        File.join(Compass.configuration.project_path, Compass.configuration.images_dir, path)
      end

      px_real_path = px_path(real_path, px)

      if File.exists?(px_real_path)
        path = px_path(path, px)
      else
         Sass::Util.sass_warn("Warning: using default pixel ratio file for " + File.basename(real_path) +
                              " (" + Pathname.new(px_real_path).cleanpath.to_s + " not found)")
      end
    end

    # we make sure to remove leading / if any, or else image-url will
    # short circuit absolute path images, and we want to stick on using static/images/...
    # as urls in our css files
    if path[0..0] == "/"
      path = path[1..-1]
    end
    Sass::Script::String.new(path)
  end

  def pixelratio_available(file, pixelratio)
    assert_type file, :String
    assert_type pixelratio, :Number

    px = pixelratio.value
    if px == 1
      true
    else
      real_path = if Compass.configuration.images_dir
        File.join(Compass.configuration.project_path, Compass.configuration.images_dir, file.value)
      end
      Sass::Script::Bool.new(File.exists?(px_path(real_path, px)))
    end
  end

  def px_path(path, px)
    path = Pathname.new(path)
    "%s/%s@%sx%s" % [path.dirname, path.basename(path.extname), px.to_s, path.extname]
  end

  declare :pixelratio_file, :args => [:file, :pixelratio]
  declare :pixelratio_available, :args => [:file, :pixelratio]

end
