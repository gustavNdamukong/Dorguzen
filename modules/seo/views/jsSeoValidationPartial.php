<?php

namespace modules\seo\views;


/**
 * Class jsSeoValidationPartial
 * @package modules/seo/views
 */
class jsSeoValidationPartial extends \DGZ_library\DGZ_HtmlView
{

    public function show()
    { ?>
        <script type="text/javascript">
            $(document).ready(function () { 
                //make an ajax call-this calls the function 'checkPageName()' below  
                $(document).on('blur', '#addPage #seo_page_name', function(e)
                {  
                    e.preventDefault(); 
                    checkPageName(this);
                });

                /**
                 * @param pageName
                 */
                function checkPageName(pageName) {
                    if (pageName.value == '') {
                        document.getElementById('info').innerHTML = '';
                        return
                    }

                    params = "pageName=" + pageName.value
                    request = new ajaxRequest()
                    request.open("POST", "seo/checkPageName", true)
                    request.setRequestHeader("Content-type",
                        "application/x-www-form-urlencoded")

                    request.onreadystatechange = function () {
                        if (this.readyState == 4) {
                            if (this.status == 200) {
                                if (this.responseText != null) {
                                    document.getElementById('info').innerHTML =
                                        this.responseText
                                }
                                else alert("Ajax error: No data received")
                            }
                            else alert("Ajax error: " + this.statusText)
                        }
                    }
                    request.send(params)
                }


                function ajaxRequest() {
                    try {
                        var request = new XMLHttpRequest()
                    }
                    catch (e1) {
                        try {
                            request = new ActiveXObject("Msxml2.XMLHTTP")
                        }
                        catch (e2) {
                            try {
                                request = new ActiveXObject("Microsoft.XMLHTTP")
                            }
                            catch (e3) {
                                request = false
                            }
                        }
                    }
                    return request
                }
            });

        </script>
        <?php
    }
}