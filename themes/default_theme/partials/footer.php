<footer>
    <div class="footer">
        <div class="container">
            <div class="row">
                <div class=" col-lg-3 col-md-6">
                    <a class="logo_bottom" href="<?= home_url(); ?>">
                        <img src="<?= theme_logo_image(); ?>" alt="#" />
                    </a>

                    <p class="many">
                        There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humou
                    </p>
                </div>
                <div class="col-lg-2 offset-lg-1 col-md-6">
                    <h3>QUICK LINKS</h3>
                    <ul class="link_menu">
                        <li><a href="index.html">Home</a></li>
                        <li><a href="about.html"> About</a></li>
                        <li><a href="project.html">Projects</a></li>
                        <li><a href="staff.html">Staff</a></li>
                        <li><a href="contact.html">Contact Us</a></li>
                    </ul>
                </div>
                <div class=" col-lg-3 col-md-6">
                    <h3>INSTAGRAM FEEDS</h3>
                    <ul class="insta text_align_left">
                        <li><img src="<?= $this->getAssetsUrl('theme/images/inst1.png'); ?>" alt="#" /></li>
                        <li><img src="<?= $this->getAssetsUrl('theme/images/inst2.png'); ?>" alt="#" /></li>
                        <li><img src="<?= $this->getAssetsUrl('theme/images/inst3.png'); ?>" alt="#" /></li>
                        <li><img src="<?= $this->getAssetsUrl('theme/images/inst4.png'); ?>" alt="#" /></li>
                    </ul>
                </div>
                <div class=" col-lg-3 col-md-6 ">
                    <h3>SIGN UP TO OUR NEWSLETTER </h3>
                    <form class="bottom_form">
                        <input class="enter" placeholder="Enter your email" type="text" name="Enter your email">
                        <button class="sub_btn">Sign Up</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="copyright">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <p>©<?= _e('{year} All Rights Reserved.', ['year' => date('Y')]); ?> <?= _e('Created by {copyright}', ['copyright' => '<a href="https://www.utas.uz/" target="_blank">www.utas.uz</a>']); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>