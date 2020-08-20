
$(document).ready(function() {
    var imageIdFromSelectBox = document.getElementById('image_id');
    var cboxParentPTag = document.getElementById('allowUpload');
    var uploadImageCheckBox = document.getElementById('upload_new');
    var uploadNewImgPTag = document.getElementById('optional');
    var newImg = document.getElementById('image');

    cboxParentPTag.style.display = 'block';
    uploadNewImgPTag.style.display = 'none';

    uploadImageCheckBox.onclick = function () {
        //the checkbox has been checked, so we set it to true, or it will be false if clicking unchecked it
        var selected = uploadImageCheckBox.checked;
        //disable the image select field
        imageIdFromSelectBox.disabled = selected;

        //parentNode of the image upload field (newImg) is the <p> tag 'uploadNewImgPTag' whose style display property is set to none initially,
        // but will be updated to match the selected status of the checkbox (true if checked and false if unchecked) when the user checks/un checks the checkbox
        // So here below we say if that <p> tag containing the newImg field is visible (its style property display value is true) (which will be if the user had clicked on the checkbox previously),
        // we keep it visible, else we hide it (none).

        //we now see 2 things
        // -i) that in JS, the display property of the style class has a value of false if its set to 'none'
        // -ii) that the '=' xter in JS is not for assigning but for evaluating the value of an element as seen below
        newImg.parentNode.style.display = selected ? 'block' : 'none';
    }


    $(document).on('click', '#uploadVideoCheckbtn', function () {
        var uploadAudio = document.getElementById('uploadAudioCheckbtn');
        var uploadImg = document.getElementById('imageInputField');


        //the ID of this upload btn could be one of two values depending on whether its the default or the 'Upload Audio' btn had been checked
        if (document.getElementById('uploadImage') !== null) {
            var uploadChoice = document.getElementById('uploadImage'); //upload submit button
        }
        else if (document.getElementById('uploadAudio') !== null) {
            var uploadChoice = document.getElementById('uploadAudio'); //upload submit button
        }
        else {
            var uploadChoice = document.getElementById('uploadVideo'); //upload submit button
        }


        //check if the 'Upload Audio' btn is already checked n un check it first
        if (uploadAudio.checked == true) {
            uploadAudio.checked = false;
            uploadImg.disabled = false;
            $('#uploadAudioField').slideUp(500);
        }


        $('#uploadVideoField').slideToggle(500, function () {
            if (uploadImg.disabled == false) {
                //img upload is active, so turn it off, as well change the text on the upload submit button to say 'Upload Audio'
                uploadImg.disabled = true;
                uploadChoice.value = 'Upload Video';
                //Give the upload btn an ID that relates to video too
                uploadChoice.id = 'uploadVideo';
            }
            else {
                //reactivate the img upload btn n change the upload btn text back
                uploadImg.disabled = false;
                uploadChoice.value = 'Upload Image';
                //Change the upload btn's ID back to the default
                uploadChoice.id = 'uploadImage';
            }
        });
    });




    $(document).on('click', '#uploadAudioCheckbtn', function () {
        var uploadVideo = document.getElementById('uploadVideoCheckbtn');
        var uploadImg = document.getElementById('imageInputField');


        //the ID of this upload btn could be one of two values depending on whether its the default or the 'Upload Audio' btn had been checked
        if (document.getElementById('uploadImage') !== null) {
            var uploadChoice = document.getElementById('uploadImage'); //upload submit button
        }
        else if (document.getElementById('uploadAudio') !== null) {
            var uploadChoice = document.getElementById('uploadAudio'); //upload submit button
        }
        else {
            var uploadChoice = document.getElementById('uploadVideo'); //upload submit button
        }


        //check if the 'Upload video' btn is already checked n un check it first
        if (uploadVideo.checked == true) {
            uploadVideo.checked = false;
            uploadImg.disabled = false;
            $('#uploadVideoField').slideUp(500);
        }


        $('#uploadAudioField').slideToggle(500, function () {
            if (uploadImg.disabled == false) {
                //img upload is active, so turn it off, as well change the text on the upload submit button to say 'Upload Audio'
                uploadImg.disabled = true;
                uploadChoice.value = 'Upload Audio';
                //Change the upload btn's ID back to the default
                uploadChoice.id = 'uploadAudio';
            }
            else {
                //reactivate the img upload btn n change the upload btn text back
                uploadImg.disabled = false;
                uploadChoice.value = 'Upload Image';
                //Change the upload btn's ID back to the default
                uploadChoice.id = 'uploadImage';
            }


        });
    });







    //handle video uploads
    $(document).on('click', '#uploadVideo', function (e) {
        e.preventDefault();

        //get the file the user selected
        var vid = document.getElementById('videoInputField');
        var file = vid.files[0];

        var params = new FormData;
        //bind the file upload field's name attribute to the FormData class
        params.append('video', file);

        //some people would rightly do 'var xhr = new XMLHttpRequest();' or 'var ajax = new XMLHttpRequest();', they're all the same
        request = new ajaxRequest();

        //grab references to a number of events about the upload that we will need, in order to monitor in this Ajax call
        // these are 4 in number: -i) progress -ii) load (fires off when uploading is complete) -iii)error -iV) abort
        //We use the 'upload' property of the ajax call as it deals with uploads, so it makes sense to add the listener on its progress event
        request.upload.addEventListener('progress', progressHandler, false);
        request.addEventListener('load', completeHandler, false);
        request.addEventListener('error', errorHandler, false);
        request.addEventListener('abort', abortHandler, false);

        request.open("POST", "blog/uploadBlogMediaAjaxCall", true);
        //before sending of the request, set the necessary headers
        request.setRequestHeader("X-File-Name", file.name);
        request.setRequestHeader("X-File-Size", file.size);

        //send the request
        request.send(params);

        request.onreadystatechange = function () {
            if (this.readyState == 4) {
                if (this.status == 200) {
                    if (this.responseText != null) {
                        //The AJAX request was successful, n 'responseText' contains your result,
                        // so do whatever you want to do with it here
                        var resultBoard = document.getElementById("uploadStatus");
                        resultBoard.style.display = 'block';
                        resultBoard.innerHTML = this.responseText;
                    }
                    else alert("Ajax error: No data received");
                }
                else alert("Ajax error: " + this.statusText);
            }
        }

    });


    //handle audios uploads
    $(document).on('click', '#uploadAudio', function (e) {
        e.preventDefault();

        //get the file the user selected
        var audio = document.getElementById('audioInputField');
        var file = audio.files[0];

        var params = new FormData;
        //bind the file upload field's name attribute to the FormData class
        params.append('audio', file);

        //some people would rightly do 'var xhr = new XMLHttpRequest();' or 'var ajax = new XMLHttpRequest();', they're all the same
        request = new ajaxRequest();

        //grab references to a number of events about the upload that we will need, in order to monitor in this Ajax call
        // these are 4 in number: -i) progress -ii) load (fires off when uploading is complete) -iii)error -iV) abort
        //We use the 'upload' property of the ajax call as it deals with uploads, so it makes sense to add the listener on its progress event
        request.upload.addEventListener('progress', progressHandler, false);
        request.addEventListener('load', completeHandler, false);
        request.addEventListener('error', errorHandler, false);
        request.addEventListener('abort', abortHandler, false);

        request.open("POST", "blog/uploadBlogMediaAjaxCall", true);
        //before sending of the request, set the necessary headers
        request.setRequestHeader("X-File-Name", file.name);
        request.setRequestHeader("X-File-Size", file.size);

        //send the request
        request.send(params);

        request.onreadystatechange = function () {
            if (this.readyState == 4) {
                if (this.status == 200) {
                    if (this.responseText != null) {
                        //The AJAX request was successful, n 'responseText' contains your result,
                        // so do whatever you want to do with it here
                        var resultBoard = document.getElementById("uploadStatus");
                        resultBoard.style.display = 'block';
                        resultBoard.innerHTML = this.responseText;
                    }
                    else alert("Ajax error: No data received");
                }
                else alert("Ajax error: " + this.statusText);
            }
        }

    });


    //create the event handler functions that the eventListeners created above will connect with to monitor the upload process
    function progressHandler(e) {
        var progressDiv = document.getElementsByClassName('progress')[0];
        var progressBar = document.getElementsByClassName('progress-bar')[0];
        progressDiv.style.display = 'block';
        var percentage = (e.loaded / e.total) * 100;
        progressBar.style.width = percentage + "%";
        progressBar.innerHTML = Math.round(percentage);
    }


    function completeHandler(e) {
        var progressDiv = document.getElementsByClassName('progress')[0];
        var progressBar = document.getElementsByClassName('progress-bar')[0];
        progressDiv.style.display = 'none';
    }


    function errorHandler(e) {
        progressBar.innerHTML = 'UPLOAD FAILED';
    }


    function abortHandler(e) {
        progressBar.innerHTML = 'UPLOAD ABORTED';
    }


    function ajaxRequest() {
        try {
            var request = new XMLHttpRequest();
        }
        catch (e1) {
            try {
                request = new ActiveXObject("Msxml2.XMLHTTP");
            }
            catch (e2) {
                try {
                    request = new ActiveXObject("Microsoft.XMLHTTP");
                }
                catch (e3) {
                    request = false;
                }
            }
        }
        return request;
    }





}); //END OF DOCUMENT READY DECLARATION




//To display or to hide the video reference text field (video module)
$(document).on('change','input[name="video_source"]',function() {
    if ($(this).val() == 'vimeo' || $(this).val() == 'youtube')
    {
        $('#video_source_reference').slideDown(30);
        $('#videoFileField').slideUp(30);
        $('#videoFileExtensionField').slideUp(30);
    }
    else 
    {
        //That means they clicked on 'local'
        $(this).val('local');
        $('#video_source_reference').slideUp(30);
        $('#videoFileField').slideDown(30);
        $('#videoFileExtensionField').slideDown(30);
    }

});




//To display or to hide the audios reference text field (audios module)
$(document).on('change','input[name="audio_source"]',function() {
    if ($(this).val() == 'soundcloud')
    {
        $('#audio_source_reference').slideDown(30);
        $('#audioFileField').slideUp(30);
        $('#audioFileExtensionField').slideUp(30);
    }
    else
    {
        //That means they clicked on 'local'
        $(this).val('local');
        $('#audio_source_reference').slideUp(30);
        $('#audioFileField').slideDown(30);
        $('#audioFileExtensionField').slideDown(30);
    }

});





$(document).on('click', '#deleteContactMessageBtn', function() {
    var confirmed = confirm('Are you sure you want to delete this contactForm message?');
    if (confirmed == true) {
        //let it flow
    } else {
        return false;
    }
})

