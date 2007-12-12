function fileQueued(fileObj) {
	try {
	    //alert('file queued');
	    
        var files_left = --$('nb_files').innerHTML;
        $('nb_files').update(files_left);
        
        // if no more file, we disable upload button
        if(files_left == 0)
        {
            $('browse_btn').disable();
        }
        
	} catch (ex) { this.debugMessage(ex); }

}

function fileProgress(fileObj, bytesLoaded) {

	try {
		var percent = Math.ceil((bytesLoaded / fileObj.size) * 100)

		var progress = new FileProgress(fileObj,  this.getSetting("upload_target"));
		progress.SetProgress(percent);
		if (percent === 100) {
			progress.SetStatus("Creating thumbnail...");
			progress.ToggleCancel(false, this);
		} else {
			progress.SetStatus("Uploading...");
			progress.ToggleCancel(true, this);
		}
	} catch (ex) { this.debugMessage(ex); }
}

function fileComplete(fileObj, server_data) {
	try {
		// upload.php returns the thumbnail id in the server_data, use that to retrieve the thumbnail for display
		//alert('file complete, server data : '+server_data);
		
		AddImage(server_data);

		var progress = new FileProgress(fileObj,  this.getSetting("upload_target"));
		//progress.SetComplete();
		progress.SetStatus("Thumbnail Created.");
		progress.ToggleCancel(false);


	} catch (ex) { this.debugMessage(ex); }
}


function fileCancelled(fileObj) {
	try {
		var progress = new FileProgress(fileObj,  this.getSetting("upload_target"));
		progress.SetCancelled();
		progress.SetStatus("Cancelled");
		progress.ToggleCancel(false);
	}
	catch (ex) { this.debugMessage(ex); }
}

function queueComplete() {
	try {
        var progress = new FileProgress({ name: "Done." },  this.getSetting("upload_target"));
        progress.SetComplete();
        progress.SetStatus("All images received.");
        progress.ToggleCancel(false);
    } catch (ex) { this.debugMessage(ex); }
}

function uploadError(error_code, fileObj, message) {
	try {
		var error_name = "";
		switch(error_code) {
			case SWFUpload.ERROR_CODE_QUEUE_LIMIT_EXCEEDED:
				error_name = "You have attempted to queue too many files.";
			break;
		}

		if (error_name !== "") {
			alert(error_name);
			return;
		}

		switch(error_code) {
			case SWFUpload.ERROR_CODE_ZERO_BYTE_FILE:
				image_name = "zerobyte.gif";
			break;
			case SWFUpload.ERROR_CODE_UPLOAD_LIMIT_EXCEEDED:
				image_name = "uploadlimit.gif";
			break;
			case SWFUpload.ERROR_CODE_FILE_EXCEEDS_SIZE_LIMIT:
				image_name = "toobig.gif";
			break;
			case SWFUpload.ERROR_CODE_HTTP_ERROR:
			case SWFUpload.ERROR_CODE_MISSING_UPLOAD_TARGET:
			case SWFUpload.ERROR_CODE_UPLOAD_FAILED:
			case SWFUpload.ERROR_CODE_IO_ERROR:
			case SWFUpload.ERROR_CODE_SECURITY_ERROR:
			default:
				alert(message);
				image_name = "error.gif";
			break;
		}

		AddImage("images/" + image_name);

	} catch (ex) { this.debugMessage(ex); }

}


/* ******************************************
 *	FileProgress Object
 *	Control object for displaying file info
 * ****************************************** */

function FileProgress(fileObj, target_id) {
	this.file_progress_id = "divFileProgress";

	this.fileProgressWrapper = document.getElementById(this.file_progress_id);
	if (!this.fileProgressWrapper) {
		this.fileProgressWrapper = document.createElement("div");
		this.fileProgressWrapper.className = "progressWrapper";
		this.fileProgressWrapper.id = this.file_progress_id;

		this.fileProgressElement = document.createElement("div");
		this.fileProgressElement.className = "progressContainer";

		var progressCancel = document.createElement("a");
		progressCancel.className = "progressCancel";
		progressCancel.href = "#";
		progressCancel.style.visibility = "hidden";
		progressCancel.appendChild(document.createTextNode(" "));

		var progressText = document.createElement("div");
		progressText.className = "progressName";
		progressText.appendChild(document.createTextNode(fileObj.name));

		var progressBar = document.createElement("div");
		progressBar.className = "progressBarInProgress";

		var progressStatus = document.createElement("div");
		progressStatus.className = "progressBarStatus";
		progressStatus.innerHTML = "&nbsp;";

		this.fileProgressElement.appendChild(progressCancel);
		this.fileProgressElement.appendChild(progressText);
		this.fileProgressElement.appendChild(progressStatus);
		this.fileProgressElement.appendChild(progressBar);

		this.fileProgressWrapper.appendChild(this.fileProgressElement);

		document.getElementById(target_id).appendChild(this.fileProgressWrapper);
		new Effect.fade(this.fileProgressWrapper);

	} else {
		this.fileProgressElement = this.fileProgressWrapper.firstChild;
		this.fileProgressElement.childNodes[1].firstChild.nodeValue = fileObj.name;
	}

	this.height = this.fileProgressWrapper.offsetHeight;

}
FileProgress.prototype.SetProgress = function(percentage) {
	this.fileProgressElement.className = "progressContainer green";
	this.fileProgressElement.childNodes[3].className = "progressBarInProgress";
	this.fileProgressElement.childNodes[3].style.width = percentage + "%";
}
FileProgress.prototype.SetComplete = function() {
	this.fileProgressElement.className = "progressContainer blue";
	this.fileProgressElement.childNodes[3].className = "progressBarComplete";
	this.fileProgressElement.childNodes[3].style.width = "";

}
FileProgress.prototype.SetError = function() {
	this.fileProgressElement.className = "progressContainer red";
	this.fileProgressElement.childNodes[3].className = "progressBarError";
	this.fileProgressElement.childNodes[3].style.width = "";

}
FileProgress.prototype.SetCancelled = function() {
	this.fileProgressElement.className = "progressContainer";
	this.fileProgressElement.childNodes[3].className = "progressBarError";
	this.fileProgressElement.childNodes[3].style.width = "";

}
FileProgress.prototype.SetStatus = function(status) {
	this.fileProgressElement.childNodes[2].innerHTML = status;
}

FileProgress.prototype.ToggleCancel = function(show, upload_obj) {
	this.fileProgressElement.childNodes[0].style.visibility = show ? "visible" : "hidden";
	if (upload_obj) {
		var file_id = this.file_progress_id;
		this.fileProgressElement.childNodes[0].onclick = function() { upload_obj.cancelUpload(file_id); return false; };
	}
}

function AddImage(src) {
	var new_img = Builder.node('img', { opacity: 0, style: 'float: left;'});
	var div = Builder.node('div', { style: 'margin: 5px; border: 1px solid black; width: 500px; clear: both; float: left;'});
	
	$("thumbnails").appendChild(div);
	
	div.appendChild(new_img);
	
	// build the div where thumb and his informations will go
	new Ajax.Updater(div, '/frontend_dev.php/images/inlineEdit', {
      asynchronous: true,
      evalScripts:  false,
      method: 'get',
      insertion: Insertion.Bottom,
      parameters: { number: $$('.ajax_inline_feedback').size() }
    });
        
    Event.observe(new_img,'load', function () {
        new Effect.Appear(new_img); 
    });
    
    new_img.src = '/uploads/images/'+src;
}

// function attachForm(div)
// {
//     // nb of div feedback on page
//     var nb_div_feedback = $$('.ajax_inline_feedback').count();
//     
//     // create required elements
//     var div_feedback_success = Builder.node('div', {
//                                                         id: 'ajax_feedback_success', 
//                                                         className: 'ajax_inline_feedback', 
//                                                         style: 'display: none;'
//                                                     }
//                                             );
//     var div_feedback_failure = Builder.node('div', {
//                                                         id: 'ajax_feedback_failure', 
//                                                         className: 'ajax_inline_feedback', 
//                                                         style: 'display: none;'
//                                                     }
//                                             );
//     
//     var form_updater =  new Ajax.Updater({
//                                           success:'ajax_feedback_success',
//                                           failure:'ajax_feedback_failure'
//                                           }, 
//                                           '/frontend_dev.php/images/save', {
//                                               asynchronous:true, 
//                                               evalScripts:true, 
//                                               onComplete:function(request, json){Element.hide('indicator'); }, 
//                                               onFailure:function(request, json){
//                                                           Element.hide('ajax_feedback_success'); 
//                                                           Element.show('ajax_feedback_failure');
//                                                           new Effect.Highlight('ajax_feedback_failure', {});
//                                                           }, 
//                                               onLoading:function(request, json){Element.show('indicator')}, 
//                                               onSuccess:function(request, json){Element.show('ajax_feedback_success'); Element.hide('ajax_feedback_failure');new Effect.Highlight('ajax_feedback_success', {});}, parameters:Form.serialize(this)}); return false;
//     
//     var form = Builder.node('form', {
//                                         action: '/frontend_dev.php/images/save',
//                                         method: 'post',
//                                         onsubmit: form_updater
//                                     });
//                                     
// 
//     var unique_name = Builder.node('input', {
//                                                 name: 'unique_name',
//                                                 value: '1186984442_1484214647.jpg',
//                                                 type: 'hidden'
//                                             });
//                                             
//     var name = Builder.node('p', {
//                                    Builder.node('input', {
//                                                            name: 'name',
//                                                            value: '',
//                                                            type: 'text'
//                                    })
//                                 }, 'name');
//     
//     var culture = Builder.node('p', {
//                                    Builder.node('input', {
//                                                            name: 'culture',
//                                                            value: '', // get the value from where...
//                                                            type: 'text'
//                                    })
//                                 }, 'culture');
//                                 
//     var description = Builder.node('p', {
//                                     Builder.node('input', {
//                                                             name: 'description',
//                                                             value: '',
//                                                             type: 'text'
//                                    })
//                                 }, 'description');
//     
//     var submit_btn = Builder.node('input', {
//                                                 name: 'commit',
//                                                 value: 'save',
//                                                 type: 'submit'
//                                             });
//     
//     // attach elements to the form
//     form.appendChild(unique_name);
//     form.appendChild(name);
//     form.appendChild(culture);
//     form.appendChild(description);
//     form.appendChild(submit_btn);
//     // attach elements to the main div
//     div.appendChild(div_feedback_success);
//     div.appendChild(div_feedback_failure);
//     div.appendChild(form);
// }