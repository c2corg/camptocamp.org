PlUploadWrapper = {

  init : function(url) {
    var uploader = new plupload.Uploader({
      runtimes : 'gears,html5,flash,silverlight,browserplus',
      browse_button : 'pickfiles',
      container : 'container',
      file_data_name : 'image_file[]',
      max_file_size : '10mb',
      multipart : true,
      url : url,
      flash_swf_url : '/static/js/plupload/plupload.flash.swf',
      silverlight_xap_url : '/static/js/plupload/plupload.silverlight.xap',
    });

    uploader.bind('Init', function(up, params) {
      $('filelist').insert("<div>Current runtime: " + params.runtime + "</div>");
    });

    uploader.init();

    uploader.bind('FilesAdded', function(up, files) {
      files.each(function(file, i) {
        $('filelist').insert(
          '<div id="' + file.id + '">' +
          file.name + ' (' + plupload.formatSize(file.size) + ') <b></b>' +
          '</div>');
      });
      up.refresh(); // Reposition Flash/Silverlight
      uploader.start(); // automatically begin upload
    });

    uploader.bind('UploadProgress', function(up, file) {
      $(file.id).down('b').insert(file.percent + "%");
    });

    uploader.bind('FileUploaded', function(up, file, response) {
      $(file.id).down('b').insert("100%");
      // show response
      $('files_to_upload').insert(response.response);
    });

    uploader.bind('Error', function(up, err) {
      $('filelist').insert("<div>Error: " + err.code +
        ", Message: " + err.message +
        (err.file ? ", File: " + err.file.name : "") +
        "</div>"
      );
      up.refresh(); // Reposition Flash/Silverlight
    });
  }
}
