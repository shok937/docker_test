<?php
define('AGRAD', 'yep!');
// Agrad Loaded AOR part (HTML)
if (isset($_POST['jquery']) && $_POST['jquery'] == 'yes')
{
	echo '<script type="text/javascript" src="libs/jquery.min.js"></script>';
}
?>
<?php
if (isset($_POST['jscookie']) && $_POST['jscookie'] == 'yes')
{
	echo '<script type="text/javascript" src="libs/jquery.cookie.js"></script>';
}
?>
<?php
if (isset($_POST['jsmask']) && $_POST['jsmask'] == 'yes')
{
    echo '<script type="text/javascript" src="libs/jquery.mask.min.js"></script>';
}
?>
<?php
if (isset($_POST['selectize']) && $_POST['selectize'] == 'yes')
{
	echo '
    <link type="text/css" rel="stylesheet" href="libs/selectize/css/selectize.css">
    <link type="text/css" rel="stylesheet" href="libs/selectize/css/selectize.default.css">
    <script type="text/javascript" src="libs/selectize/js/standalone/selectize.min.js"></script>
	';
}

// TO
$_to_content = '';
if (isset($_POST['to']) && !empty($_POST['to']))
{
    require_once(dirname(__FILE__) . '/aor-to.php');
}
$_isset_pTO = false;
if (isset($_to_data, $_selected_to_versions) && $_to_yep === true) 
{
	$_isset_pTO = true;
}
?>

<!-- Agrad O.R. Widget -->
<script type="text/javascript" src="libs/agrad-orcalendar.js?<?=time()?>"></script>
<script type="text/javascript" src="libs/agrad-orscript.js?<?=time()?>"></script>
<script type="text/javascript" src="libs/slick/slick.min.js"></script>
<link rel="stylesheet" href="libs/fonts/fonts-stylesheet.css?<?=time()?>">
<link rel="stylesheet" href="libs/agrad-orstyle.css?<?=time()?>">
<link rel="stylesheet" href="libs/slick/slick.css">
<div class="__agrad_onlinerecord_block<?php if ($_isset_pTO === true) { echo ' __oar_pretomode'; } ?>">
  <div class="__agrad_onlinerecord_overlay" style="display:none;"></div>
  <div class="__agrad_onlinerecord_inner" style="display:none;">
    <div class="__agrad_onlinerecord_close">X</div>
    <div class="__agrad_onlinerecord_body">
      <div class="__aor_tlogo"><img src="/img/agrad-logo.png" alt="Автоград" style="max-height: 50px;max-width: 130px;" /></div>
      <div class="__aor_title">Онлайн запись на сервис<br /> <!-- <font style="color: gray;font-weight: 400;">→</font> --> <span class="__aor_stepname">загружаю...</span></div>
      <div class="__aor_feedbackblock"><span class="__aor_feedbackbutton" onclick="openAFB();">Сообщить об ошибке</span></div>
      <div class="__aor_content<?php if ($_isset_pTO === true): ?> withoutbg<?php endif; ?>">

<?php 
// var_dump($_to_data);
// var_dump($_selected_to_versions);
if ($_isset_pTO === true): ?>
        <?php
        $_preto_title = 'Предложение записи на ТО'; // default
        if (isset($_to_data['op_title']) && !empty($_to_data['op_title'])) { $_preto_title = nl2br(htmlentities($_to_data['op_title'], ENT_QUOTES)); }
        $_preto_img = 'shmweel.jpg'; // default
        if (isset($_to_data['icon']) && !empty($_to_data['icon'])) { $_preto_img = htmlentities($_to_data['icon'], ENT_QUOTES); }
        ?>
        <div class="__aor_c aortorequest" style="display:none;">
            <div class="__aor_c_title_h" style="display:none;" h_num="-1"><?=$_preto_title?></div>
            <div class="__aor_c_title">
                <?=$_preto_title?>
            </div>
            <div class="__aor_c_info">
                <div class="__aor_c_inner">
					
					<div class="__aor_c_inner_pretotextblock">

	                    <div class="__aor_c_inner_img"><img src="img/<?=$_preto_img?>" /></div>

	                    <div class="__aor_c_inner_row aortornote">
	                        <b><?php /* Уважаем<?php echo $_to_data['gender'] == 1 ?'ая':'ый'; ?> */ ?><?=htmlentities($_to_data['first_name'], ENT_QUOTES)?> <?=htmlentities($_to_data['middle_name'], ENT_QUOTES)?>,</b> <br />
	                        <?php if (isset($_to_data['op_description']) && !empty($_to_data['op_description'])): ?>
                                <?=nl2br(htmlentities($_to_data['op_description'], ENT_QUOTES))?>
                            <?php else: ?>
                                у Вас подходит срок очередного<br /> Технического Обслуживания.<br /><br />
                                Для Вас сформировано<br /> персональное предложение.
	                        <?php endif; ?>
                            
                        </div>

	                    <div class="__aor_c_inner_row aortorcarname">
	                        <span class="__aor_c_ir_left">Ваш автомобиль:</span>
	                        <span class="__aor_c_ir_right">
	                            <span class="aortor-infoplace aortorcarname-place"><?php /*=htmlentities($_to_data['brand_name'], ENT_QUOTES)?> <?=htmlentities($_to_data['model_name'], ENT_QUOTES)?>, VIN <?=htmlentities($_to_data['vin'], ENT_QUOTES)?>, г/н  <?=htmlentities($_to_data['car_number'], ENT_QUOTES)*/?><?=htmlentities($_to_data['car_name'], ENT_QUOTES)?></span>
	                        </span>
	                    </div>

	                    <div class="__aor_c_inner_row aortorto">
	                        <span class="__aor_c_ir_left">Вид операции:</span>
	                        <span class="__aor_c_ir_right">
	                            <span class="aortor-infoplace aortorto-place"><?=htmlentities($_to_data['operation_name'], ENT_QUOTES)?></span>
	                        </span>
	                    </div>
					
					</div>
        
                    <div class="__aor_c_inner_row aortorcompred">
                        <div class="__aor_c_ircp_versions_title">
                            Выберите версию:
                        </div>
                        <?php foreach ($__to_groups_data as $_vers_group_k => $_vers_group_v): ?>
                        <div class="__aor_c_ircp_versions_itemslist __aor_c_ircp_versions_group_<?=(int)$_vers_group_k?>">

                        <?php foreach ($__to_groups_data[$_vers_group_k] AS $_version):

                            $_v_icon = 'oil_icon32.png'; // default
                            if (isset($_version['icon']) && !is_null($_version['icon']) && strlen($_version['icon']) > 0) { $_v_icon = $_version['icon']; }

                            $_v_summprice = (float)$_version['summ']; // итоговая (со скидкой, если есть)
                            $_v_discount = (float)$_version['disc_summ']; // сумма скидки
                            $_v_beforediscount_price = $_v_summprice+$_v_discount; // сумма до скидки

                            $_isset_discount = false;
                            if ($_v_beforediscount_price > $_v_summprice) { $_isset_discount = true; }

                            // var_dump($_v_summprice);
                            // var_dump($_v_discount);
                            // var_dump($_v_beforediscount_price);
                            // var_dump($_isset_discount);

                            ?>

                            <div class="__aor_c_ircp_versions_item_wrap">

                                <div class="__aor_c_ircp_versions_item" version_id="">
                                    <div class="__aor_c_ircp_versions_item_inner">
<!--                                         <div class="__aor_c_ircp_versions_item_checkbox">
                                            <span class="__aor_c_ircp_vi_checkbox"></span>
                                        </div> -->
                                        <div class="__aor_c_ircp_versions_item_title">                                <!-- <span class="__aor_c_ircp_versions_item_icon"><img src="img/<?=htmlentities($_v_icon, ENT_QUOTES)?>" /></span> --> <?=htmlentities($_version['operation_name'], ENT_QUOTES)?></div>
                                        <div class="__aor_c_ircp_versions_item_comment"><?=htmlentities($_version['operation_version'], ENT_QUOTES)?>, <?=htmlentities($_version['comment'], ENT_QUOTES)?></div>
                                        <div class="__aor_c_ircp_versions_item_summ">
                                            <div class="__aor_c_ircp_versions_item_checkimg"></div>
                                            <?php if ($_isset_discount === true) { echo '<span class="__aor_c_ircp_vi_beforediscount">'.number_format($_v_beforediscount_price, 0, ',', ' ').'</span> '; } ?><span class="__aor_c_ircp_vi_summ_place"><?=number_format($_v_summprice, 0, ',', ' ')?> ₽</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="__aor_c_ircp_versions_item_showmore">
                                    <a style="display: none;">состав пакета</a>
                                    <?php // услуги / товары ?>
                                    <div class="__aor_c_ircp_vi_showmore_content" style="display:none;">
                                        <div class="__aor_c_ircp_vi_showmore_itemsblock">
                                            <div class="__aor_c_ircp_vi_showmore_item __aor_c_ircp_vi_showmore_parts">
                                                <span class="__aor_c_ircp_vi_showmore_item_title">Товары</span>
                                                <span class="__aor_c_ircp_vi_showmore_item_list">
                                                    <table class="__aor_c_ircp_vi_showmore_item_ltable">
                                                    <?php // товары
                                                    foreach ($_comm_offers_array[(int)$_version['comm_offer_id']]['parts_array'] AS $_part) 
                                                    { 
                                                        $_v_part_summprice = (float)$_part['summ']; // итоговая (со скидкой, если есть)
                                                        $_v_part_discount = (float)$_part['summ_disc']; // сумма скидки
                                                        $_v_part_beforediscount_price = $_v_part_summprice+$_v_part_discount; // сумма до скидки

                                                        $_isset_part_discount = false;
                                                        if ($_v_part_beforediscount_price > $_v_part_summprice) { $_isset_part_discount = true; }
                                                    ?>
                                                        <tr iid="<?=(int)$_part['id']?>">
                                                            <td class="aortblnametd">
                                                                <?=htmlentities($_part['name'], ENT_QUOTES)?><?php if ((int)$_part['amount'] > 0): ?>, <?=number_format((int)$_part['amount'], 0, ',', ' ')?> шт<?php endif; ?>
                                                            </td> 
                                                            <td class="aortblpricetd">
                                                                <span class="__aor_c_ircp_vi_showmore_item_list_price"> 
                                                                    <?php if ($_isset_part_discount === true) { echo '<span class="__aor_c_ircp_vi_beforediscount">'.number_format($_v_part_beforediscount_price, 0, ',', ' ').'</span> '; } ?><?=number_format((float)$_part['summ'], 0, ',', ' ')?> ₽
                                                                </span>
                                                                <span style="display:block;float:none;clear:both;"></span>
                                                            </td>
                                                        </tr>
                                                    <?php 
                                                	}
                                                	if (count($_comm_offers_array[(int)$_version['comm_offer_id']]['parts_array']) < 1)
                                                	{
                                                		echo '<span class="__aor_c_ircp_vi_showmore_item_list_itemsnone">Не найдено товаров.</span>';
                                                	}

                                                    ?>
                                                    </table>
                                                </span>
                                                <span class="__aor_c_ircp_vi_showmore_item_json" style="display:none;"><?php echo rawurlencode(json_encode($_comm_offers_array[(int)$_version['comm_offer_id']]['parts_array'])); ?></span>
                                            </div>
                                            <div class="__aor_c_ircp_vi_showmore_item __aor_c_ircp_vi_showmore_works">
                                                <span class="__aor_c_ircp_vi_showmore_item_title">Услуги</span>
                                                <span class="__aor_c_ircp_vi_showmore_item_list">
                                                    <table class="__aor_c_ircp_vi_showmore_item_ltable">
                                                    <?php // услуги
                                                    foreach ($_comm_offers_array[(int)$_version['comm_offer_id']]['works_array'] AS $_work) 
                                                    { 
                                                        $_v_work_summprice = (float)$_work['summ']; // итоговая (со скидкой, если есть)
                                                        $_v_work_discount = (float)$_work['summ_disc']; // сумма скидки
                                                        $_v_work_beforediscount_price = $_v_work_summprice+$_v_work_discount; // сумма до скидки

                                                        $_isset_work_discount = false;
                                                        if ($_v_work_beforediscount_price > $_v_work_summprice) { $_isset_work_discount = true; }
                                                    ?>
                                                        <tr iid="<?=(int)$_work['id']?>">
                                                            <td class="aortblnametd">
                                                                <?=htmlentities($_work['name'], ENT_QUOTES)?>
                                                            </td> 
                                                            <td class="aortblpricetd">
                                                                <span class="__aor_c_ircp_vi_showmore_item_list_price"> 
                                                                    <?php if ($_isset_work_discount === true) { echo '<span class="__aor_c_ircp_vi_beforediscount">'.number_format($_v_work_beforediscount_price, 0, ',', ' ').'</span> '; } ?><?=number_format((float)$_work['summ'], 0, ',', ' ')?> ₽
                                                                </span>
                                                                <span style="display:block;float:none;clear:both;"></span>
                                                            </td>
                                                        </tr>
                                                    <?php 
                                                	}
                                                	if (count($_comm_offers_array[(int)$_version['comm_offer_id']]['works_array']) < 1)
                                                	{
                                                		echo '<span class="__aor_c_ircp_vi_showmore_item_list_itemsnone">Не найдено услуг.</span>';
                                                	}

                                                    ?>
                                                    </table>
                                                </span>
                                                <span class="__aor_c_ircp_vi_showmore_item_json" style="display:none;"><?php echo rawurlencode(json_encode($_comm_offers_array[(int)$_version['comm_offer_id']]['works_array'])); ?></span>
                                            </div>
                                            <div style="float:none;clear:both;"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        <?php endforeach; // versions ?> 

                        </div>
                        <?php endforeach; // groups ?> 
                    </div>

                </div>
                <div class="__aor_c_buttons">
                    <span class="__aor_c_b_i __aor_c_b_back __aor_c_b_to_notmineauto">Это не мой авто</span>
                    <span class="__aor_c_b_i __aor_c_b_next __aor_c_b_to_nexttoaction">Дальше</span>
                    <span class="__aor_c_b_i __aor_button_gotoreq" aorgo="aortorequest" style="display:none;"></span>
                    <span class="__aor_c_b_i __aor_button_gotocompanysel" aorgo="aorcompany" style="display:none;"></span>
                    <span class="__aor_c_b_i __aor_button_gotodate" aorgo="aordate" style="display:none;"></span>
                    <span class="__aor_c_b_i __aor_button_gotonotmineautosorry" aorgo="aornotmineautosorry" style="display:none;"></span>
                    <span class="__aor_inp_pretorecord" style="display:none;"></span>
                </div>
				<!--                 
				<div class="__aor_c_ptext_note">
                	...
                </div> 
            	-->

                <?php
                    // autoclick version on count ==1
                    // if (count($_selected_to_versions) == 1)
                    // {
                    //     echo '<script>$(function() { $(".__aor_c_ircp_versions_item").first().click();});</script>';
                    // }
                ?>   

            </div>
        </div>
        <div class="__aor_c aornotmineautosorry" style="display:none;">
            <div class="__aor_c_title_h" style="display:none;" h_num="-1">Это не мой авто</div>
            <div class="__aor_c_title">Извините, мы исправимся...</div>
            <div class="__aor_c_info">
                <div class="__aor_c_aornotmineautosorry_info">
                    Извините, что ошиблись с автомобилем. Мы постараемся в ближайшее время скорректировать информацию. Если у Вас есть другой автомобиль, который вы хотите записать на ТО, то вы можете воспользоваться онлайн записью.
                </div>
                <div class="__aor_c_aornotmineautosorry_orbuttonblock __aor_c_buttons">
                    <span class="__aor_c_b_i __aor_c_b_next __aor_c_aornotmineautosorry_orbutton" aorgo="aorcar">Перейти на онлайн запись</span>
                </div>
            </div>
        </div>
        <script>
        function aorTOstart() 
        {
            window.aorAuto = {
                "ref": "<?=htmlentities($_to_data['car_ref'], ENT_QUOTES);?>",
                "brand_ref": "<?=htmlentities($_to_data['brand_ref'], ENT_QUOTES)?>", 
                "brand_name": "<?=htmlentities($_to_data['brand_name'], ENT_QUOTES)?>", 
                "model_autoru_ref": "<?=htmlentities($_to_data['model_ref'], ENT_QUOTES)?>", 
                "model_autoru_name": "<?=htmlentities($_to_data['model_name'], ENT_QUOTES)?>", 
                "generation_ref": "", 
                "seria_autoru_ref": "", 
                "modification_autoru_ref": "", 
                "number": "", 
                "mileage": "<?=(int)$_to_data['mileage']?>", 
                "name": "<?=htmlentities($_to_data['car_name'], ENT_QUOTES)?>"
            };
            window.aorClient = { "ref": "<?=htmlentities($_to_data['client_ref'], ENT_QUOTES);?>" };
            window.aorPreTO = { "url": "<?=htmlentities($_to_data['external_url'], ENT_QUOTES);?>", "client": "<?=(int)$_to_data['client_id']?>", "car": "<?=(int)$_to_data['car_id']?>" };

<?php /*            window.aorCompany.ref = "8B9900215E2D367211DEBE0437ACF406";
            window.aorCompany.name = "Автоград ул.Республики, д. 268";
            window.aorCompany.address = "625014, г.Тюмень, ул.Республики,268";*/ ?>
            $('#__aorilastname_input').val('<?=htmlentities($_to_data['last_name'], ENT_QUOTES)?>');
            $('#__aorifirstname_input').val('<?=htmlentities($_to_data['first_name'], ENT_QUOTES)?>');
            <?php 
            // phone
            if (isset($_to_data['phone']) && !is_null($_to_data['phone']) && !empty($_to_data['phone'])): ?>
                $('#__aoriphone_input').val('7<?=(int)$_to_data['phone']?>').trigger('blur');
            <?php endif; ?>
            $('#__aoricomment_input').val('<?=htmlentities($_preto_title, ENT_QUOTES)?>');
            $('#__aoripdyes_input').prop('checked', true);

            $('body').css('margin', '0'); // body margin fix
            $('.__agrad_onlinerecord_inner').css('min-height', $(window).height()); // body height fix 

            $('.__aor_button_gotoreq').click(); 

            <?php if (count($__to_groups_data) == 1 && count(reset($__to_groups_data)) == 1): ?>
                <?php // автоматически выбираем эту одну версию ?>
                $(function() { 
                    $(".__aor_c_ircp_versions_item .__aor_c_ircp_versions_item_summ").first().click();
                });
            <?php else: ?>

                <?php // добавляем слайдер ?>
                // slider
                <?php foreach ($__to_groups_data as $_v_group_k => $_v_group_v): ?>
                    <?php if (count($_v_group_v) > 1): ?>
                        $('.__aor_c_ircp_versions_itemslist.__aor_c_ircp_versions_group_<?=(int)$_v_group_k?>').slick({
                            dots: true,
                            prevArrow: '<div class="__aor_slick_arrow_left"><img src="img/aor-arrow-left.png" alt="Предыдущая версия" title="Предыдущая версия" /></div>',
                            nextArrow: '<div class="__aor_slick_arrow_right"><img src="img/aor-arrow-right.png" alt="Следующая версия" title="Следующая версия" /></div>',
                        }).slick('slickGoTo', '0');
                    <?php endif; ?>
                <?php endforeach; ?>

            <?php endif; ?>

            //send upd readed
		    //encodeURIComponent(JSON.stringify(info))
		    $.getJSON('upd-info.php?upd_type=set_opened&upd_commurl='+(aorPreTO&&aorPreTO.url?encodeURIComponent(aorPreTO.url):''), function(data) { 
		    	// OK
		    }).fail(function() { 
		        console.warn("upd comm_read state error");
		    });
        }
        </script>

<?php elseif (isset($_POST['to']) && $_POST['to'] == 'basketrecord'): ?>

        <?php // создание записи из корзины ?>
        
       <div class="__aor_c_buttons aor_buttons_basketrecord" style="display:none;">
            <span class="__aor_c_b_i __aor_button_gotocompanysel" aorgo="aorcompany" style="display:none;"></span>
            <span class="__aor_c_b_i __aor_button_gotodate" aorgo="aordate" style="display:none;"></span>
            <span class="__aor_inp_pretorecord" style="display:none;"></span>
            <span class="__aor_inp_basketrecord" style="display:none;"></span>
        </div>
        <script type="text/javascript">
        function aorTOstart() 
        {
            window.jWorkParts = {};
            if ($('.__aor_frombasket_basketcomposition').length > 0 && aor_isJSON( decodeURIComponent($('.__aor_frombasket_basketcomposition').text()) ))
            {
                jWorkParts = JSON.parse(decodeURIComponent($('.__aor_frombasket_basketcomposition').text()));
            }
            window.jUserInfo = {};
            if ($('.__aor_frombasket_clientinfo').length > 0 && aor_isJSON( decodeURIComponent($('.__aor_frombasket_clientinfo').text()) ))
            {
                jUserInfo = JSON.parse(decodeURIComponent($('.__aor_frombasket_clientinfo').text()));
            }

            // console.log(jWorkParts);
            // console.log(jUserInfo);

            if (!jUserInfo['client']) { console.error('aorTOstart() error: not found jUserInfo data'); setTimeout(aorTOstart, 1000); return false; }


            window.aorAuto = jUserInfo['auto'];
            window.aorClient = { "ref": jUserInfo['client']['ref'] };
            window.aorPreTO = {}

            $('#__aorilastname_input').val(jUserInfo['client']['lastname']);
            $('#__aorifirstname_input').val(jUserInfo['client']['firstname']);
            $('#__aoriphone_input').val('7'+jUserInfo['client']['phone']).trigger('blur');
            $('#__aoricomment_input').val('');
            $('#__aoripdyes_input').prop('checked', true);


            // fill "services not found"
            if (window.aorAuto.brand_name && window.aorAuto.model_autoru_name)
            {
                $('.__aorservicenotfound_modelbrand').text(window.aorAuto.brand_name+' '+window.aorAuto.model_autoru_name);
            }

            // $('body').css('margin', '0'); // body margin fix
            // $('.__agrad_onlinerecord_inner').css('min-height', $(window).height()); // body height fix 

            // ya m goal
            if (window.ym)
            {
                ym(53948962, 'reachGoal', 'suggestionto_lkenter');
            }
              
            // rebind company and date buttons
            $('.__aor_c.aorcompany .__aor_c_b_back, .__aor_c.aorcompany .__aor_c_b_next, .__aor_c.aordate .__aor_c_b_back').hide();
            $('.__aor_button_gotodatefromcompany').show();
            
            // is akr?
            if (window.jWorkParts && window.jWorkParts.akr_data && window.jWorkParts.akr_data.d_ref)
            {
                // reset marks
                $('.__aor_inp_pretorecord, .__aor_inp_basketrecord').remove();
                $('.__aor_c_buttons.aor_buttons_basketrecord').append('<span class="__aor_inp_akrrecord" style="display:none;"></span>');

                // set d
                window.aorCompany.ref = window.jWorkParts.akr_data.d_ref;
                window.aorCompany.name = "Кузовной ремонт Автоград";
                window.aorCompany.address = "625014, г.Тюмень, ул.Республики, 274";

                // showAllTimes();
                // go date 
                $('.__aor_button_gotodate').click();
            }
            else
            {
                // choose company and next step
                getCompaniesAutomatic(); 
            }
        }
        </script>

<?php elseif (isset($_POST['to']) && $_POST['to'] == 'rerecord'): ?>

        <?php // перезапись ?>
        
       <div class="__aor_c_buttons" style="display:none;">
            <span class="__aor_c_b_i __aor_button_gotocompanysel" aorgo="aorcompany" style="display:none;"></span>
            <span class="__aor_c_b_i __aor_button_gotodate" aorgo="aordate" style="display:none;"></span>
            <span class="__aor_inp_torerecord" style="display:none;"></span>
        </div>
        <script type="text/javascript">
        function aorTOstart() 
        {
            window.jRRInfo = {};
            if ($('.__aor_fromlk_rerecordinfo').length > 0 && aor_isJSON( decodeURIComponent($('.__aor_fromlk_rerecordinfo').text()) ))
            {
                jRRInfo = JSON.parse( decodeURIComponent($('.__aor_fromlk_rerecordinfo').text()) );
            }
            window.jUserInfo = {};
            if ($('.__aor_fromlk_clientinfo').length > 0 && aor_isJSON( decodeURIComponent($('.__aor_fromlk_clientinfo').text()) ))
            {
                jUserInfo = JSON.parse( decodeURIComponent($('.__aor_fromlk_clientinfo').text()) );
            }

            // console.log(jWorkParts);
            // console.log(jUserInfo);

            if (!jUserInfo['client']) { console.error('aorTOstart() error: not found jUserInfo data'); setTimeout(aorTOstart, 1000); return false; }

            window.aorAuto = jUserInfo['auto'];
            window.aorClient = { "ref": jUserInfo['client']['ref'] };

            $('#__aorilastname_input').val(jUserInfo['client']['lastname']);
            $('#__aorifirstname_input').val(jUserInfo['client']['firstname']);
            $('#__aoriphone_input').val('7'+jUserInfo['client']['phone']).trigger('blur');
            $('#__aoricomment_input').val('');
            $('#__aoripdyes_input').prop('checked', true);

            // $('body').css('margin', '0'); // body margin fix
            // $('.__agrad_onlinerecord_inner').css('min-height', $(window).height()); // body height fix 

            // ya m goal
            if (window.ym)
            {
                ym(53948962, 'reachGoal', 'suggestionto_lkenter_rerecord');
            }
              
            // rebind company and date buttons
            $('.__aor_c.aorcompany .__aor_c_b_back, .__aor_c.aorcompany .__aor_c_b_next, .__aor_c.aordate .__aor_c_b_back').hide();
            $('.__aor_button_gotodatefromcompany').show();
            
            if (window.jRRInfo && window.jRRInfo.is_akr && window.jRRInfo.is_akr == 1)
            {
                // akr rerecord

                // set d
                window.aorCompany.ref = window.jRRInfo.dep_ref;
                window.aorCompany.name = "Кузовной ремонт Автоград";
                window.aorCompany.address = "625014, г.Тюмень, ул.Республики, 274";

                // showAllTimes();
                // go date 
                $('.__aor_button_gotodate').click();
            }
            else
            {
                // standart rerecord: choose company and next step
                getCompaniesAutomatic(); 
            }
        }
        </script>

<?php elseif ($_to_timeout === true): ?>
       <?php // timeout ?>
       <div class="__aor_c_buttons" style="display:none;"><span class="__aor_c_b_i __aor_button_gotopretotimeout" aorgo="aorpretotimeout" style="display:none;"></span></div>
        <script type="text/javascript">
        function aorTOstart() 
        {
            $('.__aor_button_gotopretotimeout').click(); 
        }
        </script>
<?php else: ?>
        <script type="text/javascript">
        function aorTOstart() 
        {
            // empty...
            console.warn('aorTOstart() function is empty result');
        }
        </script>
<?php endif; ?>

        <div class="__aor_numbers" style="display:none;">
            <div class="__aor_numbers_inner">
                <span class="__aor_numbers_item __aor_numbers_i_one __aor_ni_active" h_num="1">
                    <span class="__aor_ni_num">1</span>
                </span>
                <span class="__aor_numbers_item __aor_numbers_i_two" h_num="2">
                    <span class="__aor_ni_line"></span>
                    <span class="__aor_ni_num">2</span>
                </span>
                <span class="__aor_numbers_item __aor_numbers_i_three" h_num="3">
                    <span class="__aor_ni_line"></span>
                    <span class="__aor_ni_num">3</span>
                </span>
                <span class="__aor_numbers_item __aor_numbers_i_four" h_num="4">
                    <span class="__aor_ni_line"></span>
                    <span class="__aor_ni_num">4</span>
                </span>
                <span class="__aor_numbers_item __aor_numbers_i_five" h_num="5">
                    <span class="__aor_ni_line"></span>
                    <span class="__aor_ni_num">5</span>
                </span>
                <span class="__aor_numbers_item __aor_numbers_i_six" h_num="6">
                    <span class="__aor_ni_line"></span>
                    <span class="__aor_ni_num">6</span>
                </span>
                <span class="__aor_numbers_item __aor_numbers_i_seven" h_num="7">
                    <span class="__aor_ni_line"></span>
                    <span class="__aor_ni_num">7</span>
                </span>
                <div style="float:none;clear:both;"></div>
            </div>
        </div>
        
        <div class="__aor_c aorcar" style="display:none;">
        	<div class="__aor_c_title_h" style="display:none;" h_num="1">1. Выбор автомобиля</div>
        	<div class="__aor_c_title">Укажите Ваш автомобиль</div>
        	<div class="__aor_c_info">
        		<div class="__aor_c_inner">

        			<div class="__aor_c_inner_row aoribrand">
        				<span class="__aor_c_ir_left">Марка:</span>
        				<span class="__aor_c_ir_right">
        					<select name="aoribrand" id="__aoribrand_select">
        						<option value="">Выберите марку</option>
        					</select>
        				</span>
        			</div>
        			
        			<div class="__aor_c_inner_row aorimodel">
        				<span class="__aor_c_ir_left">Модель:</span>
        				<span class="__aor_c_ir_right">
        					<select name="aorimodel" id="__aorimodel_select">
        						<option value="">Выберите модель</option>
        					</select>
        				</span>
        			</div>
        			
        			<div class="__aor_c_inner_row aorigen">
        				<span class="__aor_c_ir_left">Поколение:</span>
        				<span class="__aor_c_ir_right">
        					<select name="aorigen" id="__aorigen_select">
        						<option value="">Выберите поколение</option>
        					</select>
        				</span>
        			</div>
        			
        			<div class="__aor_c_inner_row aoriseria">
        				<span class="__aor_c_ir_left">Серия:</span>
        				<span class="__aor_c_ir_right">
        					<select name="aoriseria" id="__aoriseries_select">
        						<option value="">Выберите серию</option>
        					</select>
        				</span>
        			</div>
        			
        			<div class="__aor_c_inner_row aorimod">
        				<span class="__aor_c_ir_left">Модификация:</span>
        				<span class="__aor_c_ir_right">
        					<select name="aorimod" id="__aorimod_select">
        						<option value="">Выберите модификацию</option>
        					</select>
        				</span>
        			</div>
        			
        			<div class="__aor_c_inner_row aorign">
        				<span class="__aor_c_ir_left">Гос. номер:<br /><span class="__aor_c_ir_minitext">(не обязательно)</span></span>
        				<span class="__aor_c_ir_right">
        					<input name="aorign" type="text" id="__aorign_input" placeholder="A 000 AA 72" style="display: none;" />

                            <input name="aorign_one" type="text" id="__aorign_input_one" placeholder="A" class="__aorign_inp_parted" />
                            <input name="aorign_two" type="text" id="__aorign_input_two" placeholder="000" class="__aorign_inp_parted" />
                            <input name="aorign_three" type="text" id="__aorign_input_three" placeholder="AA" class="__aorign_inp_parted" />
                            <input name="aorign_four" type="text" id="__aorign_input_four" placeholder="72" class="__aorign_inp_parted" />
                            <!-- <span style="display:inline-block;float:none;clear:both;"></span> -->
        				</span>
        			</div>
                    
                    <div class="__aor_c_inner_row aorimileage">
                        <span class="__aor_c_ir_left">Пробег:</span>
                        <span class="__aor_c_ir_right">
                            <input name="aorimileage" type="text" id="__aorimileage_input" placeholder="укажите пробег (в км, примерно)" />
                        </span>
                    </div>

        		</div>
        		<div class="__aor_c_buttons">
        			<span class="__aor_c_b_i __aor_c_b_next" aorgo="aorcompany">Дальше</span>
                    <span style="display:inline-block;float:none;clear:both;"></span>
        		</div>
        	</div>
        </div>

        <div class="__aor_c aorcompany" style="display:none;">
        	<div class="__aor_c_title_h" style="display:none;" h_num="2">2. Сервис</div>
        	<div class="__aor_c_title">Выберите сервис</div>
        	<div class="__aor_c_info">
        		<div class="__aor_c_inner">

        			<div class="__aor_c_inner_row aoridcompany">
        				<span class="__aor_c_ir_left">Сервис:</span>
        				<span class="__aor_c_ir_right">
        					<select name="aoridcompany" id="__aoridcompany_select">
        						<option value="">Выберите сервис</option>
        					</select>
        				</span>
        			</div>
        			
        			<div class="__aor_c_inner_row aoridaddress">
        				<span class="__aor_c_ir_left">Адрес:</span>
        				<span class="__aor_c_ir_right">
        					<span class="__aor_c_ir_addrplace">необходимо выбрать сервис</span>
        				</span>
        			</div>
        			
        			<div class="__aor_c_inner_row aorimap">
						<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
						<div id="__aor_map" style="width: 100%; height: 200px; margin: auto;"></div>
        			</div>

        		</div>
        		<div class="__aor_c_buttons">
        			<span class="__aor_c_b_i __aor_c_b_back" aorgo="aorcar">Назад</span>
                    <span class="__aor_c_b_i __aor_c_b_next __aor_button_gotodatefromcompany" aorgo="aordate" style="display:none;">Дальше</span>
        			<span class="__aor_c_b_i __aor_c_b_next" aorgo="aorservice">Дальше</span>
        		</div>
        	</div>
        </div>

        <div class="__aor_c aorservice" style="display:none;">
        	<div class="__aor_c_title_h" style="display:none;" h_num="3">3. Услуги</div>
        	<div class="__aor_c_title">Выберите услуги</div>
        	<div class="__aor_c_info">
        		<div class="__aor_c_inner">
        			
					<div class="__aor_c_inner_services">

                        <table class="__aor_packages_list">
                            <thead>
                                <tr>
                                    <th><!-- checkbox (group) --></th>
                                    <th><!-- checkbox (item) --></th>
                                    <th>Наименование</th>
                                    <th class="__aor_table_mobilehide">Пробег (км)</th>
                                    <!-- <th>Комментарий</th> -->
                                    <th class="__aor_table_mobilehide">Время <br />выполнения<br /> работ</th>
                                    <th class="__aor_table_mobilehide">Цена</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" style="text-align: center;">Данные загружаются...</td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="__aor_packages_aorotherservice">
                            <label for="__aorotherservice_input">
                                <input name="aorotherservice" type="checkbox" id="__aorotherservice_input" value="yes" style="display:none;" />
                                <span class="__aorotherservice_span"></span>
                                Другая работа
                            </label>
                        </div>

					</div>

        		</div>
        		<div class="__aor_c_buttons">
        			<span class="__aor_c_b_i __aor_c_b_back" aorgo="aorcompany">Назад</span>
        			<span class="__aor_c_b_i __aor_c_b_next" aorgo="aordate">Дальше</span>
        		</div>
        	</div>
        </div>

        <div class="__aor_c aorcarfromservices" style="display:none;">
            <div class="__aor_c_title_h" style="display:none;" h_num="-1">Дополнительная информация об автомобиле</div>
            <div class="__aor_c_title">Дополнительная информация</div>
            <div class="__aor_c_info">
                <div class="__aor_c_inner">

                    <div class="__aor_c_inner_row aorifsnote">
                        Для выбора полного списка услуг необходимо выбрать марку, модель, поколение, серию и модификацию. Если Вы не знаете точно поколение или другие параметры, то выбирайте приблизительно. На сервисе мы скорректируем информацию. 
                    </div>

                    <div class="__aor_c_inner_row aoribrandfs">
                        <span class="__aor_c_ir_left">Марка:</span>
                        <span class="__aor_c_ir_right">
                            <span id="__aoribrandfs"></span>
                        </span>
                    </div>
                    
                    <div class="__aor_c_inner_row aorimodelfs">
                        <span class="__aor_c_ir_left">Модель:</span>
                        <span class="__aor_c_ir_right">
                            <span id="__aorimodelfs"></span>
                        </span>
                    </div>
                    
                    <div class="__aor_c_inner_row aorigenfs">
                        <span class="__aor_c_ir_left">Поколение:</span>
                        <span class="__aor_c_ir_right">
                            <select name="aorigenfs" id="__aorigenfs_select">
                                <option value="">Выберите поколение</option>
                            </select>
                        </span>
                    </div>
                    
                    <div class="__aor_c_inner_row aoriseriafs">
                        <span class="__aor_c_ir_left">Серия:</span>
                        <span class="__aor_c_ir_right">
                            <select name="aoriseriafs" id="__aoriseriesfs_select">
                                <option value="">Выберите серию</option>
                            </select>
                        </span>
                    </div>
                    
                    <div class="__aor_c_inner_row aorimodfs">
                        <span class="__aor_c_ir_left">Модификация:</span>
                        <span class="__aor_c_ir_right">
                            <select name="aorimodfs" id="__aorimodfs_select">
                                <option value="">Выберите модификацию</option>
                            </select>
                        </span>
                    </div>

                </div>
                <div class="__aor_c_buttons">
                    <span class="__aor_c_b_i __aor_c_b_back" aorgo="aorservice">Отмена</span>
                    <span class="__aor_c_b_i __aor_c_b_next __aor_c_b_addcarselect" aorgo="aorservice">Дальше</span>
                    <span class="__aor_c_b_i __aor_button_gocarfs" aorgo="aorcarfromservices" style="display:none;"></span>
                    <span style="display:inline-block;float:none;clear:both;"></span>
                </div>
            </div>
        </div>

        <div class="__aor_c aordate" style="display:none;">
        	<div class="__aor_c_title_h" style="display:none;" h_num="4">4. Дата</div>
        	<div class="__aor_c_title">Выберите дату</div>
        	<div class="__aor_c_info">
        		<div class="__aor_c_inner">
        			
					<div class="__aor_c_inner_month">
						<div class="__aor_c_inner_md_title">Месяц:</div>
						<div class="__aor_c_inner_md_div"></div>
					</div>
        			
					<div class="__aor_c_inner_day">
						<div class="__aor_c_inner_md_title">День:</div>
						<div class="__aor_c_inner_md_div"></div>
					</div>

        		</div>
        		<div class="__aor_c_buttons">
        			<span class="__aor_c_b_i __aor_c_b_back" aorgo="aorservice">Назад</span>
        			<span class="__aor_c_b_i __aor_c_b_next" aorgo="aortime">Дальше</span>
        		</div>
        	</div>
        </div>

        <div class="__aor_c aortime" style="display:none;">
        	<div class="__aor_c_title_h" style="display:none;" h_num="5">5. Время</div>
        	<div class="__aor_c_title">Выберите время приёма</div>
        	<div class="__aor_c_info">
        		<div class="__aor_c_inner">
        			
					<div class="__aor_c_inner_selecteddate">
						<div class="__aor_c_inner_sd_title">Выбранная дата:</div>
						<div class="__aor_c_inner_sd_date"></div>
					</div>
        			
					<div class="__aor_c_inner_alltimes">
						<div class="__aor_c_inner_at_div"></div>
					</div>

        		</div>
        		<div class="__aor_c_buttons">
        			<span class="__aor_c_b_i __aor_c_b_back" aorgo="aordate">Назад</span>
        			<span class="__aor_c_b_i __aor_c_b_next" aorgo="aorpd">Дальше</span>
        		</div>
        	</div>
        </div>

        <div class="__aor_c aorpd" style="display:none;">
        	<div class="__aor_c_title_h" style="display:none;" h_num="6">6. Персональные данные</div>
        	<div class="__aor_c_title">Пожалуйста, укажите Ваши<br /> персональные данные</div>
        	<div class="__aor_c_info">
        		<div class="__aor_c_inner">

        			<div class="__aor_c_inner_row aorilastname">
        				<span class="__aor_c_ir_left">Фамилия:</span>
        				<span class="__aor_c_ir_right">
        					<input name="aorilastname" type="text" id="__aorilastname_input" inputmode="text" placeholder="укажите фамилию" />
        				</span>
        			</div>

        			<div class="__aor_c_inner_row aorifirstname">
        				<span class="__aor_c_ir_left">Имя:</span>
        				<span class="__aor_c_ir_right">
        					<input name="aorifirstname" type="text" id="__aorifirstname_input" inputmode="text" placeholder="укажите имя" />
        				</span>
        			</div>

        			<div class="__aor_c_inner_row aoriphone">
        				<span class="__aor_c_ir_left">Телефон:</span>
        				<span class="__aor_c_ir_right">
        					<input name="aoriphone" type="text" id="__aoriphone_input" inputmode="text" placeholder="+7 (999) 999-99-99" />
        				</span>
        			</div>

                    <div class="__aor_c_inner_row aoricomment">
                        <span class="__aor_c_ir_left">Опишите<br /> причину<br /> обращения:</span>
                        <span class="__aor_c_ir_right">
                            <textarea name="aoricomment" type="text" id="__aoricomment_input" inputmode="text" /></textarea>
                        </span>
                    </div>

        			<div class="__aor_c_inner_row aoripdyes">
        				<input name="aoripdyes" type="checkbox" id="__aoripdyes_input" value="yes" />
        				<label for="__aoripdyes_input"> Я согласен с обработкой персональных данных.</label>
        			</div>

        		</div>
        		<div class="__aor_c_buttons">
        			<span class="__aor_c_b_i __aor_c_b_back" aorgo="aortime">Назад</span>
        			<span class="__aor_c_b_i __aor_c_b_next" aorgo="aorconfirm">Дальше</span>
        		</div>
        	</div>
        </div>

        <div class="__aor_c aorconfirm" style="display:none;">
        	<div class="__aor_c_title_h" style="display:none;" h_num="7">7. Подтверждение информации</div>
        	<div class="__aor_c_title">Подтверждение информации</div>
        	<div class="__aor_c_info">
        		<div class="__aor_c_inner">

        			<div class="__aor_c_inner_row aorfinalautotitle">
        				Ваш автомобиль:
        			</div>

                    <div class="__aor_c_inner_row aorfinalcarname">
                        <span class="aorfinalcarname_text"></span>
                    </div>

        			<div class="__aor_c_inner_row aorfinalcompany">
        				<span class="__aor_c_ir_left">
        					<b>Сервис:</b>
        				</span>
        				<span class="__aor_c_ir_right">
        					<span class="aorfinalcompanyname"></span>
        					<span class="aorfinalcompanyaddress"></span>
        				</span>
        			</div>

        			<div class="__aor_c_inner_row aorfinalrdate">
        				<span class="__aor_c_ir_left">
        					<b>Дата записи:</b>
        				</span>
        				<span class="__aor_c_ir_right">
        					<span class="aorfinalrdate_text"></span>
        				</span>
        			</div>

        			<div class="__aor_c_inner_row aorfinalyoupd">
        				<span class="__aor_c_ir_left">
        					<b>Ваши данные:</b>
        				</span>
        				<span class="__aor_c_ir_right">
        					<span class="aorfinalyoupd_text"></span>
        				</span>
        			</div>

        		</div>
        		<div class="__aor_c_buttons">
        			<span class="__aor_c_b_i __aor_c_b_back" aorgo="aorpd">Изменить информацию</span>
        			<span class="__aor_c_b_i __aor_c_b_verifyphone" aorgo="aorphonecheck">Записаться</span>
                    <span class="__aor_c_b_i __aor_button_goerror" aorgo="aorerror" style="display:none;"></span>
                    <span class="__aor_c_b_i __aor_button_gosuccess" aorgo="aorsuccess" style="display:none;"></span>
                    <span class="__aor_c_b_i __aor_button_servicenotfound" aorgo="aorservicenotfound" style="display:none;"></span>
                    <span class="__aor_c_b_i __aor_button_goretime" aorgo="aorretime" style="display:none;"></span>
        		</div>
        	</div>
        </div>

        <div class="__aor_c aorphonecheck" style="display:none;">
            <div class="__aor_c_title_h" style="display:none;" h_num="-1"> Подтверждение номера телефона</div>
            <div class="__aor_c_title">Подтверждение номера телефона</div>
            <div class="__aor_c_info">
                <div class="__aor_c_inner">

                    <div class="__aor_c_inner_row aorphonecheckinfo">
                        <div class="aorphonecheckinfo_item aorphonecheckinfo_rq" style="display:none;">Отправляется SMS сообщение...</div>
                        <div class="aorphonecheckinfo_item aorphonecheckinfo_error" style="display:none;">Не получилось отправить SMS для проверки номера телефона. Попробуйте повторить запись чуть позже.</div>
                        <div class="aorphonecheckinfo_item aorphonecheckinfo_smssended" style="display:none;">На указанный Вами телефон <span class="aorphonecheckinfo_phone"></span> отправлено SMS сообщение с кодом подтверждения. Введите его в поле ниже.
                        </div>
                    </div>

                    <div class="aorphonecheckinfo_resend">
                        <div class="aorphonecheckinfo_resend_waitstep" style="display: none;">
                            Не приходит СМС? Отправьте повторное через <span class="aorphonecheckinfo_resend_timer">00:00</span> минут.
                        </div>
                        <div class="aorphonecheckinfo_resend_truestep" style="display: none;">
                            Не приходит СМС? <span class="aorphonecheckinfo_resend_link">Отправьте повторное</span>
                        </div>
                    </div>

                    <div class="__aor_c_inner_row aorphonecheckverify">
                        <span class="__aor_c_ir_left">Код подтверждения:</span>
                        <span class="__aor_c_ir_right">
                            <input name="aorphonecheckcode" type="text" id="__aorphonecheckcode_input" placeholder="00000" />
                        </span>
                    </div>

                </div>
                <div class="__aor_c_buttons">
                    <!-- <span class="__aor_c_b_i __aor_c_b_back" aorgo="aorpd">Изменить информацию</span> -->
                    <span class="__aor_c_b_i __aor_c_b_record aorstateready">Подтвердить</span>
                </div>
                <div class="__aor_c_recordnote">
                    Если у Вас долгое время не получается оформить онлайн запись, то обязательно свяжитесь с нами по номеру <b>8 (3452) 29-00-00</b>, запишитесь через оператора и расскажите, что именно у Вас не получилось при онлайн записи. <br />Спасибо, будем ждать Вас!
                </div>
            </div>
        </div>

        <div class="__aor_c aorretime" style="display:none;">
            <div class="__aor_c_title_h" style="display:none;" h_num="-1">Выбор другого времени</div>
            <div class="__aor_c_title">Выберите время приёма</div>
            <div class="__aor_c_info">
                <div class="__aor_c_inner">
                    
                    <div class="__aor_c_inner_rerecordinfo">
                        К сожалению, выбранное Вами время оказалось занято. Выберите, пожалуйста, другое время.
                    </div>
                    
                    <div class="__aor_c_inner_selecteddate">
                        <div class="__aor_c_inner_sd_title">Выбранная дата:</div>
                        <div class="__aor_c_inner_sd_date"></div>
                    </div>
                    
                    <div class="__aor_c_inner_alltimes">
                        <div class="__aor_c_inner_re_at_div"></div>
                    </div>

                </div>
                <div class="__aor_c_buttons">
                    <span class="__aor_c_b_i __aor_c_b_record __aor_c_b_rerecordbutton aorstateready">Записаться</span>
                </div>
            </div>
        </div>

        <div class="__aor_c aorsuccess" style="display:none;">
        	<div class="__aor_c_title_h" style="display:none;" h_num="-1">Готово!</div>
        	<div class="__aor_c_title">Вы успешно записаны!</div>
        	<div class="__aor_c_info">
        		Спасибо за Вашу запись на сервис. Ждём Вас <b class="__aor_print_date">XX yyyy 20XX года</b> к <b class="__aor_print_time">RR:FF</b> в сервисе <b class="__aor_print_company">Форд Республики</b> по адресу <b class="__aor_print_address">г. Тюмень, ул. Республики, 278</b>.

        	</div>
        </div>

        <div class="__aor_c aorerror" style="display:none;">
        	<div class="__aor_c_title_h" style="display:none;" h_num="-1">Ошибка</div>
        	<div class="__aor_c_title">При записи возникла ошибка...</div>
        	<div class="__aor_c_info">
        		При записи возникла какая-то ошибка. Но мы очень хотим, что бы Вы приехали к нам в сервис, поэтому обязательно позвоните по номеру <b>8 (3452) 29-00-00</b> и скажите, что Вы хотите записаться <b class="__aor_print_date">XX yyyy 20XX года</b> на <b class="__aor_print_time">RR:FF</b> в сервис <b class="__aor_print_company">ZZZZ ZZZZZ</b>.
        	</div>
        </div>


        <div class="__aor_c aorservicenotfound" style="display:none;">
            <div class="__aor_c_title_h" style="display:none;" h_num="-1">Нет сервиса для обслуживания</div>
            <div class="__aor_c_title">Нет сервиса для обслуживания</div>
            <div class="__aor_c_info">
                <div class="__aor_c_aorpretotimeout_info">
                    К сожалению для марки автомобиля "<b class="__aorservicenotfound_modelbrand">?</b>" у нас нет сервиса по обслуживанию.<br /><br />
                    Советуем вам позвонить в контакт-центр <b>8 (3452) 29-00-00</b>.
                </div>
            </div>
        </div>

        <div class="__aor_c aorpretotimeout" style="display:none;">
            <div class="__aor_c_title_h" style="display:none;" h_num="-1">Истёк срок действия</div>
            <div class="__aor_c_title">Истёк срок действия</div>
            <div class="__aor_c_info">
                <div class="__aor_c_aorpretotimeout_info">
                    Срок действия предложения записи на Техничекое Обслуживание истёк, но вы можете записаться через онлайн запись.
                </div>
                <div class="__aor_c_aorpretotimeout_orbuttonblock __aor_c_buttons">
                    <span class="__aor_c_b_i __aor_c_b_next __aor_c_aorpretotimeout_orbutton" aorgo="aorcar">Перейти на онлайн запись</span>
                </div>
            </div>
        </div>

      </div>
    </div>
  </div>
</div>
<div class="__aor-window-block">
    <div class="__aor-window-overlay" style="display: none;"></div>
    <div class="__aor-window-inner" style="display: none;">
        <div class="__aor-window-close">X</div>
        <div class="__aor-window-i-message" style="display: none;"></div>
    </div>
</div>
<div class="__aor-s-hide-date" style="display:none;">
    <span class="__aor-s-hide-date-d"><?php echo date("j"); ?></span>
    <span class="__aor-s-hide-date-m"><?php echo date("n"); ?></span>
    <span class="__aor-s-hide-date-y"><?php echo date("Y"); ?></span>
</div>
<!-- END: Agrad O.R. Widget -->
<?php
if (isset($_POST['autoclose']) && $_POST['autoclose'] == 'yes')
{
    echo '
    <script>
    $(document).on("click", ".__agrad_onlinerecord_overlay, .__agrad_onlinerecord_close", function() {
        var targetWindow = window.parent;
        targetWindow.postMessage("docloseORiframe", "*");
    });
    </script>';
}
?>