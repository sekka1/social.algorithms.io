<?php
/**
 * Main page for Signia's login page with LinkedIn Crawling
 * 
 * user:signia
 * password: 545
 */

// HTTP Login
/*
$realm = 'social.signiavc.com';
$users = array('signia' => '545');

if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Digest realm="'.$realm.
           '",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');
    die('Please Login');
}

// analyze the PHP_AUTH_DIGEST variable
if (!($data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) ||
    !isset($users[$data['username']])){
    unset($_SERVER['PHP_AUTH_DIGEST']);
    die('Wrong Credentials!');
    }
*/



// Oauthed in via LinkedIn
$didLogin = false;
if(isset($_GET["signedIn"]))
    $didLogin = $_GET["signedIn"];



?>


<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <title>Signia Social</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <!-- CSS -->
        <link rel='stylesheet' href='http://fonts.googleapis.com/css?family=PT+Sans:400,700'>
        <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/prettyPhoto/css/prettyPhoto.css">
        <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.css">
        <link rel="stylesheet" href="assets/css/social-icons.css">
        <link rel="stylesheet" href="assets/css/ebook-style.css">

        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

        <!-- Favicon and touch icons -->
        <link rel="shortcut icon" href="assets/ico/favicon.ico">

    </head>

    <body>

        <!-- Header -->
        <div class="container">
            <div class="header row">
                <div class="logo span4">
                    <h2><a href="http://signiaventurepartners.com/">Signia Venture</a></h2>
                </div>
                <div class="tel-skype span8">
                    <p><i class="icon-phone-sign"></i> Phone: 415.702.0120 <span class="pipe">|</span> <i class="icon-twitter"></i> <a href="https://twitter.com/SigniaVC">@signiavc</a></p>
                </div>
            </div>
        </div>

        <!-- Product Showcase -->
        <div class="product-showcase">
            <div class="product-showcase-pattern">
                <div class="container">
                    <div class="row">
                        <div class="span12 product-background">
                            <div class="row">
                                <div class="span5 product-image">
                                    <img src="assets/img/graph.png" alt="">
                                </div>
                                <div class="span7 product-title">
                                    
                                    <?php
                                        if($didLogin){
                                                
                                            echo "<h1>Thanks For Signing In</h1>";
                                        }else{
                                            echo '<h1>Join the Signia Network</h1>
                                                <div class="product-description">
                                                    <p>You can be a part of it!</p>
                                                    <p>You know you want to be</p>
                                                    <p>Just sign in....come on!</p>
                                                    <form action="assets/subscribe.php" method="post" class="subscribe">
                                                        <!--<input type="text" name="email" class="email" placeholder="Enter your email">-->
                                                        Sign Into LinkedIn: <a href="/linkedin.php"><img src="/social/images/social-icons/linkedin.png"/></a>
                                                    </form>';
                                        }
                                        
                                    ?>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="container">
        
        	<!--
            <div class="row">
                <div class="span12">
                    <!-features->
                    
                    <div class="features_2">
                        <h3>What's Inside</h3>
                        <div class="row">
                            <div class="span4 single-feature features-left-span">
                                <div class="feature-icon">
                                    <i class="icon-cloud"></i>
                                </div>
                                <div class="feature-text">
                                    <h4>Duis Aute Irure</h4>
                                    <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident.</p>
                                </div>
                            </div>
                            <div class="span4 single-feature">
                                <div class="feature-icon">
                                    <i class="icon-ok"></i>
                                </div>
                                <div class="feature-text">
                                    <h4>Voluptate Velit</h4>
                                    <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident.</p>
                                </div>
                            </div>
                            <div class="span4 single-feature">
                                <div class="feature-icon">
                                    <i class="icon-twitter"></i>
                                </div>
                                <div class="feature-text">
                                    <h4>Sunt In Culpa</h4>
                                    <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident.</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="span4 single-feature features-left-span single-feature-bottom">
                                <div class="feature-icon">
                                    <i class="icon-fire"></i>
                                </div>
                                <div class="feature-text">
                                    <h4>Velit Esse</h4>
                                    <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident.</p>
                                </div>
                            </div>
                            <div class="span4 single-feature single-feature-bottom">
                                <div class="feature-icon">
                                    <i class="icon-magic"></i>
                                </div>
                                <div class="feature-text">
                                    <h4>Cillum Dolore</h4>
                                    <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident.</p>
                                </div>
                            </div>
                            <div class="span4 single-feature single-feature-bottom">
                                <div class="feature-icon">
                                    <i class="icon-flag"></i>
                                </div>
                                <div class="feature-text">
                                    <h4>Fugiat Nulla</h4>
                                    <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
            -->
            <!-- end row -->

			<!--
            <div class="row">
                <div class="span12">
                    <!-gallery->
                    <div class="gallery_2">
                        <h3>Preview</h3>
                        <h5>1. Introduction to HTML5</h5>
                        <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident.</p>
                        <h5>2. Differences from HTML 4.01</h5>
                        <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident.</p>
                        <h5>3. Main features and examples</h5>
                        <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident.</p>
                        <div class="gallery-images">
                            <div class="img-wrapper">
                                <a href="assets/img/gallery/1-big.jpg" rel="prettyPhoto[pp_gal]">
                                    <img src="assets/img/gallery/1.jpg" alt="">
                                    <span class="img-background"><i class="icon-plus"></i></span>
                                </a>
                            </div>
                            <div class="img-wrapper">
                                <a href="assets/img/gallery/2-big.jpg" rel="prettyPhoto[pp_gal]">
                                    <img src="assets/img/gallery/2.jpg" alt="">
                                    <span class="img-background"><i class="icon-plus"></i></span>
                                </a>
                            </div>
                            <div class="img-wrapper">
                                <a href="assets/img/gallery/3-big.jpg" rel="prettyPhoto[pp_gal]">
                                    <img src="assets/img/gallery/3.jpg" alt="">
                                    <span class="img-background"><i class="icon-plus"></i></span>
                                </a>
                            </div>
                            <div class="img-wrapper">
                                <a href="assets/img/gallery/4-big.jpg" rel="prettyPhoto[pp_gal]">
                                    <img src="assets/img/gallery/4.jpg" alt="">
                                    <span class="img-background"><i class="icon-plus"></i></span>
                                </a>
                            </div>
                            <div class="img-wrapper">
                                <a href="assets/img/gallery/1-big.jpg" rel="prettyPhoto[pp_gal]">
                                    <img src="assets/img/gallery/1.jpg" alt="">
                                    <span class="img-background"><i class="icon-plus"></i></span>
                                </a>
                            </div>
                            <div class="img-wrapper">
                                <a href="assets/img/gallery/2-big.jpg" rel="prettyPhoto[pp_gal]">
                                    <img src="assets/img/gallery/2.jpg" alt="">
                                    <span class="img-background"><i class="icon-plus"></i></span>
                                </a>
                            </div>
                            <div class="img-wrapper">
                                <a href="assets/img/gallery/3-big.jpg" rel="prettyPhoto[pp_gal]">
                                    <img src="assets/img/gallery/3.jpg" alt="">
                                    <span class="img-background"><i class="icon-plus"></i></span>
                                </a>
                            </div>
                            <div class="img-wrapper">
                                <a href="assets/img/gallery/4-big.jpg" rel="prettyPhoto[pp_gal]">
                                    <img src="assets/img/gallery/4.jpg" alt="">
                                    <span class="img-background"><i class="icon-plus"></i></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
            -->
            
            <!-- end row -->

            <div class="row">
                <div class="span12">
                    <!-- about us -->
                    <div class="about-us_2">
                        <h3>About Us</h3>
                        <p>Signia strives to work with the world's most driven entrepreneurs.  We have developed a tool to stay connected with all those who wish to remain within the Signia network. To that end, we request that you sign up to become part of the Signia family and keep atop of upcoming news and events from within the Fund and the wider portfolio. Signing up takes 1 click and less than 10 seconds.</p>
                    </div>
                </div>
            </div> <!-- end row -->


			<!--
            <div class="row">
                <!-as seen on->
                <div class="span12 using-this">
                    <h3>As Seen On</h3>
                    <div class="using-this-container">
                        <a class="using-this-google" href="#"></a>
                        <a class="using-this-pinterest" href="#"></a>
                        <a class="using-this-youtube" href="#"></a>
                        <a class="using-this-google" href="#"></a>
                        <a class="using-this-pinterest" href="#"></a>
                    </div>
                </div>
            </div> 
            -->
            <!-- end row -->

        </div> <!-- end container -->

        <footer>
            <div class="container">
                
                <div class="row">
                </div> <!-- end row -->
                
            </div> <!-- end container -->
        </footer>


        <!-- Javascript -->
        <script src="assets/js/jquery-1.8.2.min.js"></script>
        <script src="assets/js/jquery.backstretch.min.js"></script>
        <script src="assets/js/jquery.tweet.js"></script>
        <script src="assets/prettyPhoto/js/jquery.prettyPhoto.js"></script>
        <script src="assets/js/ebook-scripts.js"></script>

    </body>

</html>


<?php
// function to parse the http auth header
function http_digest_parse($txt)
{
    // protect against missing data
    $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
    $data = array();
    $keys = implode('|', array_keys($needed_parts));

    preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

    foreach ($matches as $m) {
        $data[$m[1]] = $m[3] ? $m[3] : $m[4];
        unset($needed_parts[$m[1]]);
    }

    return $needed_parts ? false : $data;
}

?>