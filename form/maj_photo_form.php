<form id="form-photo" action="../PHP/maj_photo.php" method="POST" enctype="multipart/form-data">
                        <label for="upload-photo" class="position-relative d-inline-block" style="cursor: pointer;">
                            <?php 
                            if ($user['sexe'] == 'F') {
                                $default_image = '../Image/ProfilF.png';
                            } elseif ($user['sexe'] == 'H') {
                                $default_image = '../Image/ProfilM.png';
                            } else {
                                $default_image = '../Image/VoitureEcoride.png';
                            }

                            if (!empty($user['photo_profil']) && file_exists("../Image/" . $user['photo_profil'])) {
                                $image_path = "../Image/" . $user['photo_profil'];
                            } else {
                                $image_path = "../Image/" . $default_image;
                            }
                            ?>
                            <img src="<?php echo $image_path; ?>" 
                                class="rounded-circle mb-3 border p-1 mx-auto profile-img" 
                                width="100" height="100" 
                                style="object-fit: cover;"
                                alt="Photo de profil">
                            <div class="overlay rounded-circle d-flex align-items-center justify-content-center">
                                <span class="text-white small fw-bold">Changer ?</span>
                            </div>
                            <input type="file" name="nouvelle_photo" id="upload-photo" class="d-none" accept="image/*" onchange="document.getElementById('form-photo').submit();">
                        </label>
</form>