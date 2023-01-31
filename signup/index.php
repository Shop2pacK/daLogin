<?php

    define('TITLE', "Cadastre-se");
    include __DIR__ . '/../assets/layouts/headie_nocache.php';
    check_if_its_logged_out(); // IF THERE IS ANY AUTHORIZATION REDIRECTS TO HOME, SKIPS LOGIN

?>

<!-- HTML -->

    <!-- BODY -->   
    
        <div class="container mt-5">

            <div class="row justify-content-center mt-5">
                
                <div class="col-lg-4">
                    
                    <div class="text-center">
                        <img class="mb-1" src="../assets/images/logo.png">
                    </div>

                    <h6 class="h1 mb-3 font-weight-normal text-center"><?php echo APP_NAME; ?></h6>
                    <br>

                    <h6 class="h4 mb-3 font-weight-normal text-center">Cadastre-se</h6>

                    <form action="includes/signup2.php" method="POST" autocomplete="off">
                        
                        <!-- Placing the token -->
                        <?php _placetoken(); ?>

                        <!-- Checking if there are any errors after the form is submitted -->
                        <div class="text-center">
                            <?php
                                if (isset($_SESSION['ERRORS'])) {
                                    foreach ($_SESSION['ERRORS'] as $key=>$value) {
                                        echo "<small class=\"text-danger fw-bold\">" . $value . "</small></br>";
                                    }
                                }
                            ?>
                        </div>

                        <!-- Full Name -->
                        <div class="form-floating mb-2">
                            <input type="text" id="fullname" name="fullname" class="form-control" maxlength="40" placeholder="Nome Completo">
                            <label for="fullname">Nome Completo</label>                      
                        </div>

                        <!-- Phone and its mask -->
                        <div class="form-floating mb-2">
                            <script src="../assets/js/brazilianCelPhoneMask.js"></script>
                            <input type="text" id="phone" name="phone" class="form-control" maxlength="15" placeholder="Telefone" onkeyup="brazilianCelPhoneMask(event)">
                            <label for="phone">Telefone (00) 90000-0000 ou (00) 0000-0000</label>
                        </div>                        

                        <!-- Instagram -->
                        <div class="form-floating mb-2">                    
                            <input type="text" id="instagram" name="instagram" class="form-control" maxlength="30" placeholder="Instagram">
                            <label for="instagram">Instagram sem o @</label>                                  
                        </div>                        

                        <!-- E-Mail -->                   
                        <div class="form-floating mb-2">                    
                            <input type="email" id="email" name="email" class="form-control" maxlength="40" placeholder="Email">
                            <label for="email">Email</label>                                
                        </div>

                        <!-- Password -->                       
                        <div class="form-floating mb-2">                    
                            <input type="password" id="pwd" name="pwd" class="form-control" maxlength="40" placeholder="Senha">
                            <label for="pwd">Senha</label>                
                        </div>                                

                        <!-- Confirm Password -->                       
                        <div class="form-floating mb-2">
                            <input type="password" id="cpwd" name="cpwd" class="form-control" maxlength="40" placeholder="Confirme a senha">
                            <label for="cpwd">Confirme a senha</label>                                       
                        </div>                        

                        <!-- SUBMIT -->
                        <div class="form-group">                        
                        </div>                        
                        <div class="form-group">                        
                            <button class="btn btn-primary w-100 btn-lg" data-mdb-ripple-color="#e225d2" type="submit" name='dosignup'>Criar conta</button>
                        </div>

                        <p class="mt-3 text-muted text-center"><a href="../login/">Se você já tem uma conta clique aqui para logar</a></p>

                    </form>

                </div>

            </div>

        </div>

        <?php

            include __DIR__ . '/../assets/layouts/footie.php';

        ?>

    <!-- /BODY -->

<!-- /HTML -->