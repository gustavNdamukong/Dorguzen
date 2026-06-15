
$(document).ready(function() {

    //When a user clicks on the 'Edit' btn to edit a newsletter
    $(document).on('click','#openManageTestimonialModal',function(e) {
        //we need to grab the value of the approved field of that specific testimonial record clicked on n inject it into
        // the corresponding field of the #editManageTestimonialModal that is being opened
        var recId = $(this).data('recid');

        var approved = $('#'+recId+'_testimonials_approved').text();

        //Inject these values into the #editNewsletterModal popup form being opened
        /////$('#editManageTestimonialModal #tes_approve').val(approved);


        if (approved == 'yes')
        {
            $('#editManageTestimonialModal #tes_approve').attr('checked', 'checked');
        }
        else if (approved == 'suspended')
        {
            $('#editManageTestimonialModal #tes_suspend').attr('checked', 'checked');
        }

        $('#editManageTestimonialModal #recId').val(recId);
    });



    $(document).on('click', '#deleteTestimonialBtn', function() {
        var confirmed = confirm('Are you sure you want to delete this testimonial?');
        if (confirmed == true) {
            //let it flow
        } else {
            return false;
        }
    })


}); //END OF DOCUMENT READY DECLARATION