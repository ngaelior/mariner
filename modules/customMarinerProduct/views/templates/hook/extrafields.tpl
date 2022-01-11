<div class="m-b-1 m-t-1">
    <h2>Achile personnalisation avancée</h2>

    <fieldset class="form-group">
        <div class="col-lg-12 col-xl-4">
            <label class="form-control-label">Titre</label>
            <input type="text" name="advanced_title_0" class="form-control"
                   {if $advanced_title_0 && $advanced_title_0 != ''}value="{$advanced_title_0}"{/if}/>
        </div>
        <div class="col-lg-12 col-xl-4">
            <label class="form-control-label">Description</label>
            <textarea type="text" name="advanced_description_0" class="form-control"
                      {if $advanced_description_0 && $advanced_description_0 != ''}value="{$advanced_description_0}"{/if}>{if $advanced_description_0 && $advanced_description_0 != ''}{$advanced_description_0}{/if}</textarea>
        </div>
    </fieldset>

    <div class="custom image">
        {* Affichage du champ Image *}
        <div class="col-lg-12 col-xl-12">
            <label class="form-control-label">Image partie droite</label>

            <div id="custom_field_file_thumbnail">
                {* Si une image existe déjà sur le produit *}
                {if isset($advanced_image_0) && $advanced_image_0 !=""}
                    <div style="border: 1px dashed #000; padding:10px; max-width: 500px; margin-bottom: 30px; margin-top: 30px">
                        <img src="{$file_dir}{$advanced_image_0}" style="max-width: 450px"/><br/>
                        <a href="#" class="custom_field_file_delete">Supprimer cette image</a>
                    </div>
                {/if}
            </div>
            <div class="dropzone" id="custom_field_file_uploader" style="width: 200px">
            </div>
        </div>
        <script>
            $(function () {
                //Gestion de l'upload via la librairie DropZone
                Dropzone.autoDiscover = false;
                var customFieldImageDropzone = $('#custom_field_file_uploader').dropzone({
                    url: '{$moduleLink}&id_product={$id_product}&uploadProductImage=1&field_name=advanced_image_0',
                    paramName: "file", //Nom du champs à envoyer
                    previewTemplate: document.querySelector('#custom_field_file_dropzone_template').innerHTML,
                    thumbnailWidth: 450,
                    success: function (file) {
                        alert('image uploadée avec succès !');
                        //Affichage de la nouvelle image dans l'emplacement
                        $('#custom_field_file_thumbnail').html('').html(file.previewElement);
                    }
                });

                //Gestion de la suppression
                $('#custom_field_file_thumbnail').on('click', '.custom_field_file_delete', function () {
                    $.ajax({
                        method: 'post',
                        url: '{$moduleLink}&id_product={$id_product}&deleteProductImage=1&field_name=advanced_image_0',
                        success: function (msg) {
                            alert('{l s='File delete with success' mod='hhproduct'}');
                            $('#custom_field_file_thumbnail').html('');
                        }
                    });
                    return false;
                });
            });
        </script>

        {* template d'affichage dropzone *}
        <div id="custom_field_file_dropzone_template" style="display:none">
            <div style="border: 1px dashed #000; padding:10px; max-width: 500px; margin-bottom: 30px; margin-top: 30px">
                <div class="dz-details">
                    <label class="form-control-label">{l s='my new image' mod='hhproduct'}</label><br/>
                    <img data-dz-thumbnail style="max-width: 450px"/><br/>
                    <a href="#" class="custom_field_file_delete">{l s='Delete this image' mod='hhproduct'}</a>
                </div>
            </div>
        </div>
    </div>
    <fieldset class="form-group">
        <div class="col-lg-12 col-xl-4">
            <label class="form-control-label">Titre</label>
            <input type="text" name="advanced_title_1" class="form-control"
                   {if $advanced_title_1 && $advanced_title_1 != ''}value="{$advanced_title_1}"{/if}/>
        </div>
        <div class="col-lg-12 col-xl-4">
            <label class="form-control-label">Description</label>
            <textarea type="text" name="advanced_description_1" class="form-control"
                      {if $advanced_description_1 && $advanced_description_1 != ''}value="{$advanced_description_1}"{/if}>{if $advanced_description_1 && $advanced_description_1 != ''}{$advanced_description_1}{/if}</textarea>
        </div>
    </fieldset>

    <div class="custom image">
        {* Affichage du champ Image *}
        <div class="col-lg-12 col-xl-12">
            <label class="form-control-label">Image bas</label>

            <div id="custom_field_file_thumbnail2">
                {* Si une image existe déjà sur le produit *}
                {if isset($advanced_image_1) && $advanced_image_1 !=""}
                    <div style="border: 1px dashed #000; padding:10px; max-width: 500px; margin-bottom: 30px; margin-top: 30px">
                        <img src="{$file_dir}{$advanced_image_1}" style="max-width: 450px"/><br/>
                        <a href="#" class="custom_field_file_delete">Supprimer cette image</a>
                    </div>
                {/if}
            </div>
            <div class="dropzone" id="custom_field_file_uploader2" style="width: 200px">
            </div>
        </div>
        <script>
            $(function () {
                //Gestion de l'upload via la librairie DropZone
                Dropzone.autoDiscover = false;
                var customFieldImageDropzone = $('#custom_field_file_uploader2').dropzone({
                    url: '{$moduleLink}&id_product={$id_product}&uploadProductImage=1&field_name=advanced_image_1',
                    paramName: "file", //Nom du champs à envoyer
                    previewTemplate: document.querySelector('#custom_field_file_dropzone_template2').innerHTML,
                    thumbnailWidth: 450,
                    success: function (file) {
                        alert('image uploadée avec succès !');
                        //Affichage de la nouvelle image dans l'emplacement
                        $('#custom_field_file_thumbnail2').html('').html(file.previewElement);
                    }
                });

                //Gestion de la suppression
                $('#custom_field_file_thumbnail2').on('click', '.custom_field_file_delete', function () {
                    $.ajax({
                        method: 'post',
                        url: '{$moduleLink}&id_product={$id_product}&deleteProductImage=1&field_name=advanced_image_1',
                        success: function (msg) {
                            alert('{l s='File delete with success' mod='hhproduct'}');
                            $('#custom_field_file_thumbnail2').html('');
                        }
                    });
                    return false;
                });
            });
        </script>

        {* template d'affichage dropzone *}
        <div id="custom_field_file_dropzone_template2" style="display:none">
            <div style="border: 1px dashed #000; padding:10px; max-width: 500px; margin-bottom: 30px; margin-top: 30px">
                <div class="dz-details">
                    <label class="form-control-label">{l s='my new image' mod='hhproduct'}</label><br/>
                    <img data-dz-thumbnail style="max-width: 450px"/><br/>
                    <a href="#" class="custom_field_file_delete">{l s='Delete this image' mod='hhproduct'}</a>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</div>