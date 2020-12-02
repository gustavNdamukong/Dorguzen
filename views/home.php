<?php

namespace views;





class home extends \DGZ_library\DGZ_HtmlView
{ 
     


     function show()
     {
         //Use the addMetadata() method to add description, keywords, and author details to the head tag of your view. This is critical for SEO. Check the layout files to
         //see which generic meta tags have been used that apply to all views in your app and determine which ones you need to add on the fly for specific views.
         $this->addMetadata(
             [
                 '<meta name="description" content="Welcome to DorguzApp (replace this with your application name).">',
                 '<meta name="keywords" content="DorguzApp (replace this with your application name) contact form. Separate multiple keywords by commas">',
                 '<meta name="author" content="Your name here">'
             ]);
         ?>
         <!-- ==========================
    	BREADCRUMB - START
		=========================== -->
         <section class="breadcrumb-wrapper" xmlns="http://www.w3.org/1999/html">
             <div class="container">
                 <div class="row">
                     <div class="col-xs-6">
                     </div>
                     <div class="col-xs-6">
                         <ol class="breadcrumb">
                             <li><a></a></li>
                         </ol>
                     </div>
                 </div>
             </div>
         </section>
         <!-- ==========================
             BREADCRUMB - END
         =========================== -->

         <!-- ==========================
             MAIN PAGE - START
         =========================== -->
         <section class="content news">
             <div class="container">
                 <h2 class="hidden">Home</h2>
                 <div class="row">

                     <div class="viewText">
                         <h2>Welcome to the Dorguzen Framework</h2>

                         <p>
                             Alright we know you have heard about many PHP development frameworks out there, so the pressure is on. What do we tell you about Dorguzen to
                             make it stand out?
                             Well...there are so many points we can make but it is really true what they say; that the proof is in the pudding. As opposed to other
                             frameworks which make you spend a lot of time trying to decipher the code and get your head around all the abstractions, the Dorguzen framework
                             strives to <span class="text-primary">cut to the chase</span> and give you what you need to get up and running with your development.
                             It is a lightweight but at the same time a very powerful PHP development framework that comes packed full with tools to get you going.
                             </p>

                             <p>Its key hallmarks are:</p>
                            <ul>
                                 <li>Very simple (as a matter of fact, over-simplified routing</li>
                                 <li>Simple HTTP API system-there are only a handful or character sets you can pass in the browser URL to make your web pages come alive</li>
                                 <li>Simple <span class="text-primary">Object Relational Mapping (ORM)</span> system built into its models</li>
                                 <li>A model-database chaining system based on the rule of 'convention over configuration' which if followed will give you a great head start</li>
                                <li>Dorguzen aims to strip out the over-complicated learning curves involved with modern frameworks, and hand the reigns of control to you
                                    the engineer/developer as quickly as possible. You will be pleased to find how easy it is to get under the hood and tweak absolutely
                                    every part of the code base - and you are actually encouraged to do so, in order to learn about what it does. This hands-on type of
                                    approach is known to be the best way to gain mastery in any craft. You will be surprised at what you would be able to build in no time.
                                </li>
                                <li>
                                     Helper classes for just about anything you are likely to run into when developing like <span class="text-primary">file uploading and resizing </span>,
                                     easy <span class="text-primary">PDF creation </span>, bootstrap and font awesome libraries pulled in to help you create sleek user interfaces
                                     that are fully responsive, and much much more...
                                 </li>
                            </ul>
                             <p>In short, the Dorguzen framework is, <span class="text-primary">Your Rapid Web Development Toolkit</span>
                         </p>


                     </div>
                 </div><!--END OF ROW DIV, THE FIRST ELEMENT INSIDE THE CONTAINER DIV - WH WRAPS AROUND, OR IS FOLLOWED (INSIDE OF IT) BY THE COL-SM-9 DIV THAT HOLDS THE MAIN BODY OF THE PAGE-->
             </div><!--END OF CONTAINER DIV-->
         </section>
         <!-- ==========================
             MAIN PAGE - END
         =========================== -->
         <section>

        </section>
          <?php
     }

}

?>