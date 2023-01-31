window.aorInfo = {}; window.aorAuto = {}; window.aorCompany = {}; window.aorCompaniesList = {}; window.aorService = {}; window.aorServiceList = {}; window.aorDateTime = {}; window.aorPersonal = {};

$(function() {
  
  // first window (car)
  $(".__aor_c.aorcar").fadeIn();
  // set title
  $(".__aor_stepname").text($(".__aor_c.aorcar .__aor_c_title_h").text());
  
  // draw calendar
  aor_drawMonths();
  aor_setCalendar();
  
  // phone
  $("#__aoriphone_input").mask("+7 (999) 999-99-99");
  
  // close window
  $(".__agrad_onlinerecord_overlay, .__agrad_onlinerecord_close").click(function () {
      // hide
      $("div.__agrad_onlinerecord_block .__agrad_onlinerecord_inner").fadeOut("fast", function () {
          $("div.__agrad_onlinerecord_overlay").fadeOut("fast");
      });
  });
  
  // buttons
  // next/back
  $(document).on('click', '.__aor_c_buttons .__aor_c_b_i', function() {
    var aorgo = $(this).attr('aorgo');

    if (typeof aorgo !== typeof undefined && aorgo !== false) 
    {
      let thisStepBlock = $(this).parents('.__aor_c'); // this step block
      
      // check requirements
      var carBrand = $('#__aoribrand_select').val();
      var carModel = $('#__aorimodel_select').val();
      if ( (carBrand.length < 1 || carModel.length < 1) && aorgo == 'aorcompany' && !$(this).hasClass('__aor_button_gotocompanysel') ) 
      {
        aorAlert('Необходимо заполнить поля: Марка, Модель и др.'); 
        return false;
      }
      
      // load companies on 2nd step
      if (aorgo == 'aorcompany' && !$(this).hasClass('__aor_button_gotocompanysel'))
      {
        // gn empty check
        if ( ($("#__aorign_input_one").val().length > 0 || $("#__aorign_input_two").val().length > 0 || $("#__aorign_input_three").val().length > 0 || $("#__aorign_input_four").val().length > 0) && ($("#__aorign_input_one").val().length < 1 || $("#__aorign_input_two").val().length < 1 || $("#__aorign_input_three").val().length < 2 || $("#__aorign_input_four").val().length < 1) )
        {
          aorAlert('Если Вы хотите указать гос. номер автомобиля, то нужно его заполнить полностью.'); 
          return false;
        }
        else if ($("#__aorign_input_one").val().length > 0 && $("#__aorign_input_two").val().length > 0 && $("#__aorign_input_three").val().length > 0 && $("#__aorign_input_four").val().length > 0)
        {
          // isset gn, save to aorign input
          $('#__aorign_input').val($("#__aorign_input_one").val()+$("#__aorign_input_two").val()+$("#__aorign_input_three").val()+$("#__aorign_input_four").val());
        }
        // uppercase car number
        $('#__aorign_input').val($('#__aorign_input').val().toUpperCase());
      }
      
      // check service before 3rd step
      if (aorgo == 'aorservice' && $('#__aoridcompany_select').val().length < 10)
      {
        aorAlert('Необходимо выбрать сервис, в который Вы хотите обратиться.'); 
        return false;
      }
      // check deps before datetime steps (only in preTO MODE)
      if (aorgo == 'aordate' && $('#__aoridcompany_select').val().length < 10 && $(this).hasClass('__aor_button_gotodatefromcompany'))
      {
        aorAlert('Необходимо выбрать сервис, в который Вы хотите обратиться.'); 
        return false;
      }
      // check date before 5rd step
      if (aorgo == 'aortime' && (!window.aorDateTime || !window.aorDateTime.date || window.aorDateTime.date.length < 5))
      {
        aorAlert('Необходимо выбрать дату, на которую Вы хотите записаться.'); 
        return false;
      }
      // check date before 6rd step
      if (aorgo == 'aorpd' && $(".__aor_at_timeitem.timeselected").length == 0)
      {
        aorAlert('Необходимо выбрать время, на которое Вы хотите записаться.'); 
        return false;
      }

      // calc selected packages
      let packagesArr = window.aorServiceList ? window.aorServiceList : {};
      let selectedPackagesCount = 0;
      for (var i in packagesArr)
      {
        if (packagesArr[i]['p_selected'] && packagesArr[i]['p_selected'] == 1)
        {
          selectedPackagesCount++;
        }
      }
      // check pd before 7rd step
      if (aorgo == 'aorconfirm' && ($('#__aorilastname_input').val().length < 1 || $('#__aorifirstname_input').val().length < 1 || $('#__aoriphone_input').val().length < 1 || ($('#__aoricomment_input').val().length < 1 && selectedPackagesCount < 1) ))
      {
        // redstyle
        // last name
        if ($('#__aorilastname_input').val().length < 1)
          $('#__aorilastname_input').addClass('redstyle');
        else
          $('#__aorilastname_input').removeClass('redstyle');
        // first name
        if ($('#__aorifirstname_input').val().length < 1)
          $('#__aorifirstname_input').addClass('redstyle');
        else
          $('#__aorifirstname_input').removeClass('redstyle');
        // phone
        if ($('#__aoriphone_input').val().length < 1)
          $('#__aoriphone_input').addClass('redstyle');
        else
          $('#__aoriphone_input').removeClass('redstyle');
        // reason
        if ($('#__aoricomment_input').val().length < 1)
          $('#__aoricomment_input').addClass('redstyle');
        else
          $('#__aoricomment_input').removeClass('redstyle');

        aorAlert('Заполните, пожалуйста, Ваши имя, фамилию, телефон и кратко опишите причину обращения.'); 
        return false;
      }
      else
      {
        // normalize
        $('#__aorilastname_input, #__aorifirstname_input, #__aoriphone_input, #__aoricomment_input').removeClass('redstyle');
      }
      if (aorgo == 'aorconfirm' && !$('#__aoripdyes_input').is(':checked'))
      {
        aorAlert('Для продолжения необходимо согласиться с обработкой персональных данных. Дайте своё согласие, пожалуйста.'); 
        return false;
      }
      
      // step 1 (aorcar) save
      if (thisStepBlock.hasClass('aorcar'))
      {
        let mileageVal = parseInt($('#__aorimileage_input').val().replace(/\D/g,''), 10);
        if (isNaN(mileageVal)) {mileageVal = 0;} // null mileage fix 
        
        // form name string
        let nameVal = ($("#__aoribrand_select").val().length > 5 ? $("#__aoribrand_select").text() : ''); //brands
        nameVal += ($("#__aorimodel_select").val().length > 5 ? ', '+$("#__aorimodel_select").text() : ''); //models
        nameVal += ($("#__aorigen_select").val().length > 5 ? ', '+$("#__aorigen_select").text() : ''); //generation
        nameVal += ($("#__aoriseries_select").val().length > 5 ? ', '+$("#__aoriseries_select").text() : ''); //series
        nameVal += ($("#__aorimod_select").val().length > 5 ? ', '+$("#__aorimod_select").text() : ''); //modification
        nameVal += ($("#__aorign_input").val().length > 0 ? ', г/н '+$("#__aorign_input").val().toUpperCase().replace(/ /g,"") : ''); //car number
        nameVal += ($("#__aorimileage_input").val().length > 0 ? ', '+$("#__aorimileage_input").val() : ''); //mileage
        
        window.aorAuto = {"brand_ref": $("#__aoribrand_select").val(), "brand_name": $("#__aoribrand_select").text(), "model_autoru_ref": $("#__aorimodel_select").val(), "model_autoru_name": $("#__aorimodel_select").text(), "generation_ref": $("#__aorigen_select").val(), "seria_autoru_ref": $("#__aoriseries_select").val(), "modification_autoru_ref": $("#__aorimod_select").val(), "number": $("#__aorign_input").val().toUpperCase().replace(/ /g,""), "mileage": mileageVal, "name": nameVal};
      }
      
      // load companies on 2nd step
      if (aorgo == 'aorcompany')
      {
        getCompanies();
      }
      
      // load gen/ser/mod on 3.1 step (additional)
      //if (aorgo == 'aorcarfromservices')
      //{
      //  getGeneration(new Date().getFullYear());
      //}
      if ($(this).hasClass('__aor_c_b_addcarselect'))
      {
        // check fsfields
        if ($("#__aorigenfs_select").val().length < 5 || $("#__aoriseriesfs_select").val().length < 5 || $("#__aorimodfs_select").val().length < 5)
        {
          aorAlert('Необходимо выбрать поколение, серию и модель.'); 
          return false;
        }
        
        // update gen/ser/mod
        window.aorAuto.generation_ref = $("#__aorigenfs_select").val();
        window.aorAuto.seria_autoru_ref = $("#__aoriseriesfs_select").val();
        window.aorAuto.modification_autoru_ref = $("#__aorimodfs_select").val();
      }
      
      // load services on 3rd step
      if (aorgo == 'aorservice')
      {
        getPackages();
        
        // preload 3 days times 
        let dThisDate = parseInt($('.__aor-s-hide-date-d').text(), 10);
        let mThisDate = parseInt($('.__aor-s-hide-date-m').text(), 10)-1;
        let yThisDate = parseInt($('.__aor-s-hide-date-y').text(), 10);
        let todayDateObj = new Date(yThisDate, mThisDate, dThisDate);
        let todayDate = aor_printDateFromObj(todayDateObj);
        let tomorrowDate = aor_printDateFromObj( new Date(todayDateObj.getTime() + 86400000) );
        let afterTomorrowDate = aor_printDateFromObj( new Date(todayDateObj.getTime() + (86400000*2)) );
        //console.log(todayDate);
        //console.log(tomorrowDate);
        //console.log(afterTomorrowDate);
        $.getJSON('get-info.php?f=datetime&dep='+window.aorCompany.ref+'&date='+todayDate, function(predata) {});
        $.getJSON('get-info.php?f=datetime&dep='+window.aorCompany.ref+'&date='+tomorrowDate, function(predata) {});
        $.getJSON('get-info.php?f=datetime&dep='+window.aorCompany.ref+'&date='+afterTomorrowDate, function(predata) {});
      }
      
      // load times before 4 step
      if (aorgo == 'aortime')
      {
        showAllTimes();
      }
      
      // reload finaly data (before 7 step)
      if (aorgo == 'aorconfirm')
      { 
        //save pd
        window.aorPersonal = {'firstname': $("#__aorifirstname_input").val(), 'lastname': $("#__aorilastname_input").val(), 'phone': $("#__aoriphone_input").val(), 'confirm': 'yes'};
        // reload data
        printAllRecordData();
      }
      
      // sms
      if (aorgo == 'aorphonecheck')
      {
        aorsmsStep();
      }
      
      // bg for perto
      if (aorgo == 'aortorequest')
      {
        $(".__aor_content").addClass('withoutbg');
      }
      else if ($(".__aor_content").hasClass('withoutbg'))
      {
        $(".__aor_content").removeClass('withoutbg');
      }
      
      // change shown windows
      $(".__aor_c").hide();
      $(".__aor_c."+aorgo).fadeIn();
      // reset title
      $(".__aor_stepname").text($(".__aor_c."+aorgo+" .__aor_c_title_h").text());
      // change number step
      let nextNumberStep = $(".__aor_c."+aorgo+" .__aor_c_title_h").attr("h_num");
      if (typeof nextNumberStep !== typeof undefined && nextNumberStep !== false && parseInt(nextNumberStep, 10) > 0)
      {
        $(".__aor_numbers_item").removeClass("__aor_ni_active"); // clear all atives
        // activate all needed steps
        $(".__aor_numbers_item").each(function(index) {
          let thispNumberStep = $(this).attr("h_num");
          if (typeof thispNumberStep !== typeof undefined && thispNumberStep !== false && parseInt(thispNumberStep, 10) <= parseInt(nextNumberStep, 10))
          {
            $(this).addClass("__aor_ni_active");
          }
        });
        // show
        $(".__aor_numbers").show();
        
      }
      else
        $(".__aor_numbers").hide();
      
      
      // ya m goals
      if (window.ym)
      {
        if (aorgo == 'aorcompany') // 1
          ym(53948962, 'reachGoal', 'fill_auto');
        else if (aorgo == 'aorservice') // 2
          ym(53948962, 'reachGoal', 'fill_company');
        else if (aorgo == 'aordate') // 3
          ym(53948962, 'reachGoal', 'select_services');
        else if (aorgo == 'aortime') // 4
          ym(53948962, 'reachGoal', 'select_date');
        else if (aorgo == 'aorpd') // 5
          ym(53948962, 'reachGoal', 'select_time');
        else if (aorgo == 'aorconfirm') // 6
          ym(53948962, 'reachGoal', 'fill_personaldata');
        else if (aorgo == 'aorphonecheck') // 7
          ym(53948962, 'reachGoal', 'final_confirm');
      }
      
      
    }
  });
  
  // selectize autoload
  $('#__aoribrand_select, #__aorimodel_select').selectize({
    plugins: ['remove_button'],
    sortField: {
      field: 'count',
      direction: 'desc'
    },
  });
  $('#__aoriseries_select, #__aoriseriesfs_select, #__aorimod_select, #__aorimodfs_select').selectize({
    plugins: ['remove_button'],
    sortField: {
      field: 'text',
      direction: 'asc'
    },
  });
  // selectize custom 2 (generation)
  $('#__aorigen_select, #__aorigenfs_select, #__aoridcompany_select').selectize({
    plugins: ['remove_button'],
    sortField: {
      field: 'text',
      direction: 'asc'
    },
    searchField: ['text', 'years'],
    render: {
        item: function(item, escape) {
        //console.log(item);
            return '<div class="__aor-s-s-item">' +
                (item.text ? '<span class="__aor-s-sname">' + escape(item.text) + '</span>' : '') +
            '</div>';
        },
        option: function(item, escape) {
        //console.log(item);
            var label = item.text || item.years;
            var caption = item.text ? item.years : null;
            return '<div class="__aor-s-s-option">' +
                '<span class="__aor-s-slabel">' + escape(label) + '</span>' +
                (caption ? '<span class="__aor-s-scaption">' + escape(caption) + '</span>' : '') +
            '</div>';
        }
    },
  });
  
  // test
  //getBrands();

});

// suggestion TO, next action
$(document).on('click', '.__aor_c_b_to_nexttoaction', function() {
  
  // check selected version
  if ($('.__aor_c_ircp_versions_item.selected').length < 1) 
  {
    // ya m goal
    if (window.ym)
    {
      ym(53948962, 'reachGoal', 'suggestionto_warn_needselectversion');
    }
    aorAlert('Выберите, пожалуйста, версию.'); 
    return false;
  }
  
  // ya m goal
  if (window.ym)
  {
    ym(53948962, 'reachGoal', 'suggestionto_next');
  }
  
  // rebind company and date buttons
  $('.__aor_c.aorcompany .__aor_c_b_back, .__aor_c.aorcompany .__aor_c_b_next, .__aor_c.aordate .__aor_c_b_back').hide();
  $('.__aor_button_gotodatefromcompany').show();
  
  // choose company and next step
  getCompaniesAutomatic(); 
});

// suggestion TO, not mine auto
$(document).on('click', '.__aor_c_b_to_notmineauto', function() {
  
  //todo: add data
  let notmineautoData = {
    'car_ref': (window.aorAuto && window.aorAuto.ref && window.aorAuto.ref.length > 5 ? window.aorAuto.ref : null), 
    'client_ref': (window.aorClient && window.aorClient.ref && window.aorClient.ref.length > 5 ? window.aorClient.ref : null)
  };
  $.getJSON('send-notmineauto.php?d='+encodeURIComponent(JSON.stringify(notmineautoData)), function(data) {
    console.log(data);
    
    if (data && data.status && data.status=='error')
    {
      // ya m goal
      if (window.ym)
      {
        ym(53948962, 'reachGoal', 'suggestionto_error_notmineauto');
      }
      
      aorAlert("Не удалось отправить информацию (b). Попробуйте, пожалуйста, чуть позже.");
    }
    else
    {
      // OK (all ok - its default?)
      
      // ya m goal
      if (window.ym)
      {
        ym(53948962, 'reachGoal', 'suggestionto_notmineauto');
      }
      
      // go to notmineautosorry
      $('.__aor_button_gotonotmineautosorry').click();
      // remove preto flag
      $('.__aor_inp_pretorecord').remove();
      
      // upd state
      //encodeURIComponent(JSON.stringify(info))
      $.getJSON('upd-info.php?upd_type=not_mine_auto&upd_client='+(aorPreTO&&aorPreTO.client?encodeURIComponent(aorPreTO.client):'')+'&upd_car='+(aorPreTO&&aorPreTO.car?encodeURIComponent(aorPreTO.car):''), function(data) { 
        // OK
      }).fail(function() { 
        console.warn("upd notmineauto state error");
      });
    }
    
  }).fail(function() {
    //clear
    aorAlert("Не удалось отправить информацию. Попробуйте, пожалуйста, чуть позже.");
    // ya m goal
    if (window.ym)
    {
      ym(53948962, 'reachGoal', 'suggestionto_error_notmineauto');
    }
  });
  
});

// suggestion TO, show more
$(document).on('click', '.__aor_c_ircp_versions_item_title', function() {
  let smContent = $(this).parents('.__aor_c_ircp_versions_item_wrap').find('.__aor_c_ircp_versions_item_showmore .__aor_c_ircp_vi_showmore_content').html();
  aorAlert(smContent, 'html');
});

// auto actions
$(document).on('change', '#__aoribrand_select', function() {
  getModels();
});
$(document).on('change', '#__aorimodel_select', function() {
  getGeneration(new Date().getFullYear());
});
$(document).on('change', '#__aorigen_select', function() {
  getSeries();
});
$(document).on('change', '#__aorigenfs_select', function() {
  getSeriesFS();
});
$(document).on('change', '#__aoriseries_select', function() {
  getModification();
});
$(document).on('change', '#__aoriseriesfs_select', function() {
  getModificationFS();
});
$(document).on('change', '#__aorimod_select', function() {
  // ??
});

// mileage trigger
$(document).on('change', '#__aorimileage_input', function() {
  // ??
});

// address
$(document).on('change', '#__aoridcompany_select', function() {
  
  // empty old map
  if (window.myMap)
  {
    window.myMap.destroy();
  }
  $('#__aor_map').empty();
  
  if ($(this).val().length > 5)
  {
    window.aorCompany.ref = window.aorCompaniesList[$(this).val()]['dep_ref'];
    window.aorCompany.name = window.aorCompaniesList[$(this).val()]['dep_full_name'];
    window.aorCompany.address = window.aorCompaniesList[$(this).val()]['dep_address'];
    
    // set text
    $('.__aor_c_ir_addrplace').text(window.aorCompaniesList[$(this).val()]['dep_address']);
      
    
    // redraw map
        var geocoder = new ymaps.geocode(
            // Строка с адресом, который нужно геокодировать
            window.aorCompaniesList[$(this).val()]['dep_address'],
            // требуемое количество результатов
            { results: 1 }
        );
        // После того, как поиск вернул результат, вызывается callback-функция
        geocoder.then(
                function (res) {
                    // координаты объекта
                    var coord = res.geoObjects.get(0).geometry.getCoordinates();
                    window.myMap = new ymaps.Map('__aor_map', {
                        // Центр карты - координаты первого элемента
                        center: coord,
                        // Коэффициент масштабирования
                        zoom: 16,
                    }),
                    placemark = new ymaps.Placemark(coord, {
                        iconContent: window.aorCompany.name,
                        balloonContent: '<b>'+window.aorCompany.name+'</b><br />'+window.aorCompany.address,
                    }, {
                        // Запретим замену обычного балуна на балун-панель.
                        balloonPanelMaxMapArea: 0,
                        draggable: false,
                        preset: "islands#blueStretchyIcon",
                        // Заставляем балун открываться даже если в нем нет содержимого.
                        openEmptyBalloon: true
                    });
            
                    // После того как метка была создана, добавляем её на карту.
                    window.myMap.geoObjects.add(placemark);

                }
        );
  
  }
  else
    $('.__aor_c_ir_addrplace').text('необходимо выбрать сервис');

});

// PACKAGES
$(document).on('click', '.__aor_packages_item', function() {
  let packageID = $(this).attr("package-id");
  let clickeditemGroupID = $(this).attr("group-id");
  let clickeditemUniqueID = $(this).attr('unique-id');
  let clickeditemVersion = $(this).attr('version');
  
  // check selected item on subpackages ("doubles versions")
  let issetCheckedSubpackage = false;
  $('table.__aor_packages_list tr.__aor_packages_item').each(function( index ) {
    var thisPackageID = $(this).attr('package-id');
    var thisUniqueID = $(this).attr('unique-id');
    if (typeof thisPackageID !== typeof undefined && thisPackageID !== false && typeof packageID !== typeof undefined && packageID !== false && thisPackageID == packageID && $(this).hasClass('selected') && thisUniqueID != clickeditemUniqueID) {
      issetCheckedSubpackage = true;
    }
  });
  if (issetCheckedSubpackage == true) {aorAlert('Одна из версий услуг уже была выбрана в данной группе. Если хотите выбрать другую, то сначала снимите галочку с уже выбранной услуги.'); return false;}
  
  if (typeof packageID !== typeof undefined && packageID !== false && window.aorServiceList[packageID]) {
    let thisPackage = window.aorServiceList[packageID];
    
    $('tr.__aor_packages_item').removeClass('active');
    $(this).addClass('active'); // set active in list
    
    if (thisPackage['p_selected'] && thisPackage['p_selected'] == 1)
    {
      thisPackage['p_selected'] = 0;
      $(this).removeClass('selected'); // unset selected
      thisPackage['version_selected'] = ''; // clear selected version
      
      $(this).find('.cb-packs-ind').html('');
    }
    else
    {
      // set active in global array
      thisPackage['p_selected'] = 1;
      $(this).addClass('selected'); // set selected
      thisPackage['version_selected'] = $(this).attr("version"); // add selected version
        
      $(this).find('.cb-packs-ind').html('<b>✓</b>');
    }
    
  }
  else
  {
    //aorAlert('Для выбора данной услуги необходимо указать дополнительные данные (поколение/серия/модификация).');
    // additional window
    $(".__aor_c_b_i.__aor_button_gocarfs").click();
    
    // set brand and model
    $("#__aoribrandfs").text(window.aorAuto.brand_name);
    $("#__aorimodelfs").text(window.aorAuto.model_autoru_name);
    
    // load gen
    getGenerationFS(new Date().getFullYear());
  }
  
});
// group open/close
$(document).on('click', '.__aor_packages_group', function() {
  
  var subpackagesID = $(this).attr('subpackages-id');
  var groupID = $(this).attr('group-id');
  
  if (!$(this).hasClass('opened'))
  {
    // do open
    
    // show
    $('table.__aor_packages_list tr.__aor_packages_item').each(function( index ) {
      var thisGroupID = $(this).attr('group-id');
      if (typeof thisGroupID !== typeof undefined && thisGroupID !== false && thisGroupID == groupID) 
      {
        $(this).show();
      }
    });
    
    // finalize
    $(this).find(".cb-packs-group-ind").text("➖"); // - (bold)
    $(this).addClass('opened')
  }
  else
  {
    // do close
    
    // check selected item on subpackages
    let issetCheckedSubpackage = false;
    $('table.__aor_packages_list tr.__aor_packages_item').each(function( index ) {
      var packageID = $(this).attr('package-id');
      if (typeof packageID !== typeof undefined && packageID !== false && packageID == subpackagesID && $(this).hasClass('selected')) {
        issetCheckedSubpackage = true;
      }
    });
    
    if (issetCheckedSubpackage == false)
    {
      // hide
      $('table.__aor_packages_list tr.__aor_packages_item').each(function( index ) {
        var thisGroupID = $(this).attr('group-id');
        if (typeof thisGroupID !== typeof undefined && thisGroupID !== false && thisGroupID == groupID) {
          $(this).hide();
        }
      });
      
      $(this).find(".cb-packs-group-ind").text("➕"); // + (bold)
      $(this).removeClass('opened')
    }
    else
    {
      aorAlert('Нельзя свернуть группу, так как в ней уже выбрана услуга. Вначале снимите галочку с выбранной услуги.');
    }
  }
  
});
// set other package
$(document).on('click', '.__aor_packages_aorotherservice label', function() {
  let thisOtherPackageF = $(this).find('.__aorotherservice_span');
  if (thisOtherPackageF.text().length < 1)
  {
    // set
    thisOtherPackageF.html('<b>✓</b>');
    return false;
  }
  else
  {
    thisOtherPackageF.empty(); //clear
    return false;
  }
});

// TO compred selection (.__aor_c_ircp_versions_item)
$(document).on('click', '.__aor_c_ircp_versions_item_summ', function() {
  // selection
  if (!$(this).parents('.__aor_c_ircp_versions_item').hasClass('selected'))
  {
    // check double selection
    let issetSelectedVItem = false;
    $('.__aor_c_ircp_versions_item').each(function() 
    {
      if ($(this).hasClass('selected')) 
      { 
        issetSelectedVItem = true; 
      }
    });
    if (issetSelectedVItem === false)
    {
      $('.__aor_c_ircp_versions_item').removeClass('selected'); // clear all selected
      // set
      //thisOtherCompredF.html('<b>✓</b>');
      $(this).parents('.__aor_c_ircp_versions_item').addClass('selected');
      return false;
    }
    else
    {
      aorAlert('Можно выбрать только одну версию.');
      return false;
    }
  }
  else
  {
    $(this).parents('.__aor_c_ircp_versions_item').removeClass('selected');
    return false;
  }
});

// date
$(document).on('click', '.__aor_selmonth_div', function() {
  // month
  $('.__aor_selmonth_div').removeClass('monthselected');
  $(this).addClass('monthselected');
  // reload calendar
  aor_setCalendar($(this).attr('smonth'), $(this).attr('syear'));
});
$(document).on('click', 'td.__aor_day_td', function() {
  // date
  if (!$(this).hasClass('__aor_past_day')) // check "not past record"
  {
    $('td.__aor_day_td').removeClass('dateselected');
    $(this).addClass('dateselected');
    // set val
    let dateVal = $(this).attr('sdate');
    if (typeof dateVal !== typeof undefined && dateVal !== false)
    {
      window.aorDateTime.date = dateVal;
      $('.__aor_c_inner_sd_date').text(window.aorDateTime.date.replace(/-/g, '.'));
    }
  }
  else
  {
    aorAlert('К сожалению нельзя записаться на уже прошедшие дни.');
  }
});
// time
$(document).on('click', '.__aor_at_timeitem', function() {
  
  if (!$(this).hasClass('__aor_notavaliable_time')) // check avaliable
  {
    $('.__aor_at_timeitem').removeClass('timeselected');
    $(this).addClass('timeselected');
    // set vals
    let startVal = $(this).attr('start');
    let endVal = $(this).attr('end');
    let advisVal = $(this).attr('adviser');
    if (typeof startVal !== typeof undefined && startVal !== false && typeof endVal !== typeof undefined && endVal !== false && typeof advisVal !== typeof undefined && advisVal !== false)
    {
      //window.aorAdvisers
      window.aorDateTime.start = startVal;
      window.aorDateTime.end = endVal;
      window.aorDateTime.adviser = advisVal;
      window.aorDateTime.adviser_name = window.aorAdvisers[advisVal];
    }
  }
  else
  {
    aorAlert('К сожалению, на данное время всё занято. Можете выбрать другое?');
  }
});

// record action
$(document).on('click', '.__aor_c_b_record.aorstateready', function() {
  
  // rerecord check
  if ($(this).hasClass('__aor_c_b_rerecordbutton'))
  {
    if ($('.__aor_c.aorretime .__aor_c_inner_re_at_div .__aor_at_timeitem.timeselected').length < 1) // not select time
    {
      aorAlert('Необходимо выбрать время приёма.');
      return false;
    }
  }
  
  // build array
  let packagesArr = window.aorServiceList ? window.aorServiceList : {};
  // minify packages array
  let packagesArrMini = {};
  var mlen = packagesArr.length;
  for (var i in packagesArr)
  {
    if (packagesArr[i]['p_selected'] && packagesArr[i]['p_selected'] == 1)
    {
      // todelete rebuild
      let todeleteTMP = { "services": {}, "products": {} };
      // to final array
      packagesArrMini[packagesArr[i]['ref']] = { 
        "ref": packagesArr[i]['ref'],
        "version": packagesArr[i]['version_selected'],
        "todelete": todeleteTMP,
      };
    }
  }
  
  // data
  let aorRecordData = {
    "auto": {
      "ref": (window.aorAuto && window.aorAuto.ref && window.aorAuto.ref.length > 5 ? window.aorAuto.ref : null),
      "vin": "",
      "name": window.aorAuto.name,
      "number": window.aorAuto.number.length > 0 ? window.aorAuto.number : null,
      "mileage": window.aorAuto.mileage, //default
      "brand_ref": window.aorAuto.brand_ref, //default
      "model_autoru_ref": window.aorAuto.model_autoru_ref, //default
      "generation_ref": window.aorAuto.generation_ref, //default
      "seria_autoru_ref": window.aorAuto.seria_autoru_ref, //default
      "modification_autoru_ref": window.aorAuto.modification_autoru_ref, //default
      "packages": packagesArrMini,
    },
    "department": {
      "ref": window.aorCompany.ref,
      "name": window.aorCompany.name,
      "address": window.aorCompany.address,
    },
    "client": {
      "ref": (window.aorClient && window.aorClient.ref && window.aorClient.ref.length > 5 ? window.aorClient.ref : null),
      "name": window.aorPersonal.lastname+" "+window.aorPersonal.firstname,
      "phone": window.aorPersonal.phone,
      "confirm": window.aorPersonal.confirm,
    },
    "event": {
      "ref": null,
    },
    "contragent": {
      "ref": null,
      "name": "",
    },
    "record": {
      "manager": {
        "ref": window.aorDateTime.adviser,
        "name": window.aorDateTime.adviser_name,
      },
      "reason": $('#__aoricomment_input').val(), //"Онлайн запись"
      "begin": window.aorDateTime.start,
      "end": window.aorDateTime.end,
    },
    "order": {},
    "invoice": {},
    "task": {},
    "promises": [],
    "record_type": "online_record",
  };
  
  if ($('#__aorphonecheckcode_input').val().length < 1) // code is required
  {
    aorAlert('Необходимо указать код подтверждения.');
    return false;
  }
  
  $.getJSON('sms.php?f=verify&rid='+window.aorInfo['rid']+'&timestamp='+window.aorInfo['timestamp']+'&phone='+(window.aorPersonal['phone'].replace(/\D/g,''))+'&code='+($('#__aorphonecheckcode_input').val().replace(/\D/g,'')), function(smsdata) {
    
    if (smsdata && smsdata.status && smsdata.info && smsdata.status == "error" && smsdata.info == "not_found_smsverification")
    {
      aorAlert('Внутренняя ошибка: не получилось отправить SMS на ваш номер или операция прошла с ошибкой. Свяжитесь, пожалуйста, со службой поддержки. Спасибо.');
    }
    else if (smsdata && smsdata.status && smsdata.info && smsdata.status == "error" && smsdata.info == "try_limit_exceeded")
    {
      aorAlert('Превышено количество попыток для ввода кода подверждения. Попробуйте отправить повторную СМС.');
    }
    else if (smsdata && smsdata.status && smsdata.info && smsdata.status == "error" && smsdata.info == "operation_timeout")
    {
      aorAlert('Истек срок действия отправленного SMS.');
    }
    else if (smsdata && smsdata.status && smsdata.info && smsdata.status == "error" && smsdata.info == "wrong_code")
    {
      aorAlert('Неверный код подтверждения.');
    }
    else if (smsdata && smsdata.status && smsdata.status == "ok")
    {
      
      // ya m goals
      if (window.ym)
      {
        ym(53948962, 'reachGoal', 'sms_confirm');
      }
      
      let recordQAdditional = '';
      if ($('.__aor_inp_pretorecord').length > 0) // preto record type
      {
        recordQAdditional = '&record_type=preto';
        // update data auto -> packages
        aorRecordData['auto']['packages'] = { 'works': [], 'parts': [] };
        aorRecordData['auto']['recalls_list'] = [];
        aorRecordData['auto']['recalls_arrays'] = [];
        // basket composition
        if ($('.__aor_inp_basketrecord').length > 0)
        {
          aorRecordData['auto']['packages']['works'] = window.jWorkParts['works'];
          aorRecordData['auto']['packages']['parts'] = window.jWorkParts['parts'];
          // recalls
          if (window.jWorkParts['recalls_list'] && window.jWorkParts['recalls_list'].length>0)
          {
            aorRecordData['auto']['recalls_list'] = window.jWorkParts['recalls_list'];
          }
          if (window.jWorkParts['recalls_arrays'] && window.jWorkParts['recalls_arrays'].length>0)
          {
            aorRecordData['auto']['recalls_arrays'] = window.jWorkParts['recalls_arrays'];
          }
          // detect recalls and update reason
          if (window.jWorkParts['recalls_arrays'] && window.jWorkParts['recalls_arrays'].length>0)
          {
            let reasonRecallAdd = '';
            for (var rcpart in window.jWorkParts['recalls_arrays'])
            {
              
              if (window.jWorkParts['recalls_arrays'][rcpart] && window.jWorkParts['recalls_arrays'][rcpart]['def_op_code'])
              {
                if (reasonRecallAdd.length == 0)
                {
                  reasonRecallAdd += ' (';
                }
                else
                {
                  reasonRecallAdd += ', ';
                }
                reasonRecallAdd += '+ отзывная № '+window.jWorkParts['recalls_arrays'][rcpart]['def_op_code'];
              }
            }
            if (reasonRecallAdd.length > 0)
            {
              reasonRecallAdd += ')';
              // update data 
              aorRecordData['record']['reason'] += reasonRecallAdd;
            }
          }
        }
        // select preto works & parts
        else if ($('.__aor_c_ircp_versions_item.selected').length > 0)
        {
        
          // get works
          let workInSelectedVersion = [];
          workInSelectedVersion_text = $('.__aor_c_ircp_versions_item.selected').parents('.__aor_c_ircp_versions_item_wrap').find('.__aor_c_ircp_versions_item_showmore .__aor_c_ircp_vi_showmore_item.__aor_c_ircp_vi_showmore_works .__aor_c_ircp_vi_showmore_item_json').text();
          if (aor_isJSON(decodeURIComponent(workInSelectedVersion_text)))
          {
            workInSelectedVersion = JSON.parse(decodeURIComponent(workInSelectedVersion_text));
          }
          
          // get parts
          let partInSelectedVersion = [];
          partInSelectedVersion_text = $('.__aor_c_ircp_versions_item.selected').parents('.__aor_c_ircp_versions_item_wrap').find('.__aor_c_ircp_versions_item_showmore .__aor_c_ircp_vi_showmore_item.__aor_c_ircp_vi_showmore_parts .__aor_c_ircp_vi_showmore_item_json').text();
          if (aor_isJSON(decodeURIComponent(partInSelectedVersion_text)))
          {
            partInSelectedVersion = JSON.parse(decodeURIComponent(partInSelectedVersion_text));
          }
          
          //console.log(workInSelectedVersion);
          //console.log(partInSelectedVersion);
          
          aorRecordData['auto']['packages']['works'] = workInSelectedVersion;
          aorRecordData['auto']['packages']['parts'] = partInSelectedVersion;
          
        }
        

        // update discounted summ and price (parts)
        if (aorRecordData['auto']['packages']['parts'].length>0)
        {
          for (trpart in aorRecordData['auto']['packages']['parts'])
          {
            let thisPart = aorRecordData['auto']['packages']['parts'][trpart];

            if (thisPart && thisPart['summ_disc'] && thisPart['summ'] && thisPart['price'] && thisPart['amount']) // && parseInt(thisPart['summ_disc'],10) > 0
            {
              // magic
              // aorRecordData['auto']['packages']['parts'][trpart]['summ'] = parseFloat(thisPart['summ'])+parseFloat(thisPart['summ_disc']);
              aorRecordData['auto']['packages']['parts'][trpart]['summ'] = parseFloat(thisPart['price'])*parseFloat(thisPart['amount']);
              // aorRecordData['auto']['packages']['parts'][trpart]['price'] = parseFloat(thisPart['price'])+(parseFloat(thisPart['summ_disc'])/parseFloat(thisPart['amount']));
              aorRecordData['auto']['packages']['parts'][trpart]['price'] = parseFloat(thisPart['price']);
            }
          }
        }


        // update discounted summ and price (works)
        if (aorRecordData['auto']['packages']['works'].length>0)
        {
          for (trwork in aorRecordData['auto']['packages']['works'])
          {
            let thisWork = aorRecordData['auto']['packages']['works'][trwork];

            if (thisWork && thisWork['summ_disc'] && thisWork['summ'] && thisWork['price'] && thisWork['amount'] && thisWork['work_hour'] && parseInt(thisWork['summ_disc'],10) > 0)
            {
              // magic
              aorRecordData['auto']['packages']['works'][trwork]['summ'] = parseFloat(thisWork['summ'])+parseFloat(thisWork['summ_disc']);
              // price need correct
              aorRecordData['auto']['packages']['works'][trwork]['price'] = parseFloat(thisWork['price'])*parseFloat(thisWork['amount'])*parseFloat(thisWork['work_hour']);
            }
          }
        }

        console.log(JSON.stringify(aorRecordData));

        // send record (preto)
        $('.__aor_c_b_record').removeClass('aorstateready'); // unactive record button
        $.post( 'send-record.php?f=m'+recordQAdditional+'&rid='+window.aorInfo['rid']+'&timestamp='+window.aorInfo['timestamp']+'&phone='+(window.aorPersonal['phone'].replace(/\D/g,'')), { 'd': JSON.stringify(aorRecordData) })
          .done(function( rdata ) 
        {
          $('.__aor_c_b_record').addClass('aorstateready'); // active record button
          
          if (rdata && rdata.length > 0 && aor_isJSON(rdata)) { rdata = JSON.parse(rdata); }
          aor_record_success(rdata);
          //send upd recorded
          if (rdata && rdata.r_ref && rdata.r_ref.length > 5)
          {
            //encodeURIComponent(JSON.stringify(info))
            
            var targetWindow = window.parent; if (targetWindow) {targetWindow.postMessage("doSuccessRecordActions", "*");}
            
            $.getJSON('upd-info.php?upd_type=set_recorded&upd_commurl='+(aorPreTO&&aorPreTO.url?encodeURIComponent(aorPreTO.url):''), function(data) { 
              // OK
            }).fail(function() { 
              console.warn("upd comm_record state error");
            });
            
            generateICSfile({
              'ics_start': window.aorDateTime.start,
              'ics_end': window.aorDateTime.end,
              'ics_loc': window.aorCompany.address,
              'ics_desc': 'Вы записаны на сервис ' + window.aorCompany.name,
              'ics_url': 'https://lk.agrad.ru/',
              'ics_title': 'Запись на сервис Автоград',
            });
          }
        }).fail(function() {
          $('.__aor_c_b_record').addClass('aorstateready'); // active record button
          aor_record_fail();
        });
      }
      else if ($('.__aor_inp_akrrecord').length > 0)
      {
        recordQAdditional = '&record_type=akrrecord';

        // add akr data 
        if (window.jWorkParts && window.jWorkParts.akr_data)
        {
          aorRecordData['akr_data'] = window.jWorkParts.akr_data;
        }

        console.log(JSON.stringify(aorRecordData));

        // send record (akrrecord)
        $('.__aor_c_b_record').removeClass('aorstateready'); // unactive record button
        $.post( 'send-record.php?f=m'+recordQAdditional+'&rid='+window.aorInfo['rid']+'&timestamp='+window.aorInfo['timestamp']+'&phone='+(window.aorPersonal['phone'].replace(/\D/g,'')), { 'd': JSON.stringify(aorRecordData) })
          .done(function( rdata ) 
        {
          $('.__aor_c_b_record').addClass('aorstateready'); // active record button
          
          if (rdata && rdata.length > 0 && aor_isJSON(rdata)) { rdata = JSON.parse(rdata); }
          aor_record_success(rdata);
          //send upd recorded
          if (rdata && rdata.r_ref && rdata.r_ref.length > 5)
          {
            //encodeURIComponent(JSON.stringify(info))
            
            var targetWindow = window.parent; if (targetWindow) {targetWindow.postMessage("doSuccessRecordActions", "*");}
            
            // $.getJSON('upd-info.php?upd_type=set_recorded&upd_commurl='+(aorPreTO&&aorPreTO.url?encodeURIComponent(aorPreTO.url):''), function(data) { 
            //   // OK
            // }).fail(function() { 
            //   console.warn("upd comm_record state error");
            // });
            
            generateICSfile({
              'ics_start': window.aorDateTime.start,
              'ics_end': window.aorDateTime.end,
              'ics_loc': window.aorCompany.address,
              'ics_desc': 'Вы записаны на сервис ' + window.aorCompany.name,
              'ics_url': 'https://lk.agrad.ru/',
              'ics_title': 'Запись на сервис Автоград',
            });
          }
        }).fail(function() {
          $('.__aor_c_b_record').addClass('aorstateready'); // active record button
          aor_record_fail();
        });

      }
      else if ($('.__aor_inp_torerecord').length > 0) // rerecord type
      {
        // new data
        let aorRecordData = {
          "bp_ref": (jRRInfo['bp_ref']?jRRInfo['bp_ref']:null),
          "dep_ref": (jRRInfo['dep_ref']?jRRInfo['dep_ref']:null),
          "is_akr": (jRRInfo['is_akr']?jRRInfo['is_akr']:null),
          "order_ref": (jRRInfo['order_ref']?jRRInfo['order_ref']:null),
          "order_repair_ref": (jRRInfo['order_repair_ref']?jRRInfo['order_repair_ref']:null),
          "r_ref": (jRRInfo['r_ref']?jRRInfo['r_ref']:null),
          "event_ref": null,
          "record": {
            "manager": {
              "ref": window.aorDateTime.adviser,
              "name": window.aorDateTime.adviser_name,
            },
            "reason": $('#__aoricomment_input').val(), //"Перезапись"
            "begin": window.aorDateTime.start,
            "end": window.aorDateTime.end,
          },
          "record_type": "rerecord",
        };
        
        console.log(JSON.stringify(aorRecordData));

        // send record (rerecord)
        $('.__aor_c_b_record').removeClass('aorstateready'); // unactive record button
        $.post( 'send-record.php?f=m&record_type=rerecord&rid='+window.aorInfo['rid']+'&timestamp='+window.aorInfo['timestamp']+'&phone='+(window.aorPersonal['phone'].replace(/\D/g,'')), { 'd': JSON.stringify(aorRecordData) })
          .done(function( rdata ) 
        {
          $('.__aor_c_b_record').addClass('aorstateready'); // active record button
          
          if (rdata && rdata.length > 0 && aor_isJSON(rdata)) { rdata = JSON.parse(rdata); }
          aor_record_success(rdata);
          //send upd recorded
          if (rdata && rdata.r_ref && rdata.r_ref.length > 5)
          {
            // rerecord is OK
            //encodeURIComponent(JSON.stringify(info))
            //$.getJSON('upd-info.php?upd_type=set_recorded&upd_commurl='+(aorPreTO&&aorPreTO.url?encodeURIComponent(aorPreTO.url):''), function(data) { 
              // OK
            //}).fail(function() { 
            //  console.warn("upd comm_record state error");
            //});

            generateICSfile({
              'ics_start': window.aorDateTime.start,
              'ics_end': window.aorDateTime.end,
              'ics_loc': window.aorCompany.address,
              'ics_desc': 'Вы записаны на сервис ' + window.aorCompany.name,
              'ics_url': 'https://lk.agrad.ru/',
              'ics_title': 'Запись на сервис Автоград',
            });

          }
        }).fail(function() {
          $('.__aor_c_b_record').addClass('aorstateready'); // active record button
          aor_record_fail();
        });
      }
      else
      {
        console.log(JSON.stringify(aorRecordData));

        // send record (standart)
      
        var aorRecordDataStr = encodeURIComponent(JSON.stringify(aorRecordData));
        
        $('.__aor_c_b_record').removeClass('aorstateready'); // unactive record button
        $.getJSON('send-record.php?f=m&d='+aorRecordDataStr+'&rid='+window.aorInfo['rid']+'&timestamp='+window.aorInfo['timestamp']+'&phone='+(window.aorPersonal['phone'].replace(/\D/g,'')), function(rdata) {
          $('.__aor_c_b_record').addClass('aorstateready'); // active record button
          aor_record_success(rdata);

          generateICSfile({
            'ics_start': window.aorDateTime.start,
            'ics_end': window.aorDateTime.end,
            'ics_loc': window.aorCompany.address,
            'ics_desc': 'Вы записаны на сервис ' + window.aorCompany.name,
            'ics_url': 'https://lk.agrad.ru/',
            'ics_title': 'Запись на сервис Автоград',
          });

        }).fail(function() {
          $('.__aor_c_b_record').addClass('aorstateready'); // active record button
          aor_record_fail();
        });
      }
      //alert('record');
      //$('.__aor_button_gosuccess').click();
    }
    else
    {
      $('.__aor_button_goerror').click();
      
      // ya m goals
      if (window.ym)
      {
        ym(53948962, 'reachGoal', 'fail_record');
      }
    }
    
  }).fail(function() {
    $('.__aor_button_goerror').click();
    
    // ya m goals
    if (window.ym)
    {
      ym(53948962, 'reachGoal', 'fail_record');
    }
  });
  
});
// sms resend
$(document).on('click', '.aorphonecheckinfo_resend_link', function() {
  // hide "buttonplaces"
  $('.aorphonecheckinfo_resend_waitstep').hide();
  $('.aorphonecheckinfo_resend_truestep').hide();
  // send
  aorsmsStep();
});

function generateICSfile(iscdata=false) 
{
  if (!iscdata || !iscdata.ics_start || !iscdata.ics_end || !iscdata.ics_loc || !iscdata.ics_desc || !iscdata.ics_url || !iscdata.ics_title) { return false; }

  $('body').append('<iframe id="dwnldICSiframe" style="display:none;"></iframe>');
  document.getElementById('dwnldICSiframe').src = 'dwnl_ics_calendar_file.php?ics_start='+encodeURIComponent(iscdata.ics_start)+'&ics_end='+encodeURIComponent(iscdata.ics_end)+'&ics_loc='+encodeURIComponent(iscdata.ics_loc)+'&ics_desc='+encodeURIComponent(iscdata.ics_desc)+'&ics_url='+encodeURIComponent(iscdata.ics_url)+'&ics_title='+encodeURIComponent(iscdata.ics_title);
}

function aor_record_success(rdata) 
{
  //console.log(rdata);
  if (rdata && rdata.r_ref && rdata.r_ref.length > 5)
  {
    $('.__aor_button_gosuccess').click();
    
    // ya m goals
    if (window.ym)
    {
      ym(53948962, 'reachGoal', 'success_record');
    }
  }
  else if (rdata && rdata.WarningText && (rdata.WarningText.indexOf('Время занято') !== -1 || rdata.WarningText.indexOf('свободного мастера-приемщика') !== -1) )
  {
    // (time is busy)
    
    // ya m goals
    if (window.ym)
    {
      ym(53948962, 'reachGoal', 'reselect_time');
    }
    
    // upd c
    $.getJSON('get-info.php?clear_cache=record_datetime&dep='+window.aorCompany.ref+'&date='+window.aorDateTime.date, function(cchdata) {
      
      // retime
      showAllTimes('retime'); // load
      $('.__aor_c_b_i.__aor_button_goretime').click(); // window trigger
      
    }).fail(function() {
      $('.__aor_button_goerror').click();
      
      // ya m goals
      if (window.ym)
      {
        ym(53948962, 'reachGoal', 'fail_record');
      }
    });
    
  }
  else
  {
    $('.__aor_button_goerror').click();
    console.log(rdata);
    
    // ya m goals
    if (window.ym)
    {
      ym(53948962, 'reachGoal', 'fail_record');
    }
  }
}
function aor_record_fail(rdata) 
{
  $('.__aor_button_goerror').click();
  
  // ya m goals
  if (window.ym)
  {
    ym(53948962, 'reachGoal', 'fail_record');
  }
}

function aor_gn_autoreplace_chars(thisValue='', charsLimit=-1) 
{
  thisValue = thisValue.toUpperCase();
  // А, В, Е, К, М, Н, О, Р, С, Т, У и Х (12)
  thisValue = thisValue.replace(/А/g, "A");
  thisValue = thisValue.replace(/В/g, "B");
  thisValue = thisValue.replace(/Е/g, "E");
  thisValue = thisValue.replace(/К/g, "K");
  thisValue = thisValue.replace(/М/g, "M");
  thisValue = thisValue.replace(/Н/g, "H");
  thisValue = thisValue.replace(/О/g, "O");
  thisValue = thisValue.replace(/Р/g, "P");
  thisValue = thisValue.replace(/С/g, "C");
  thisValue = thisValue.replace(/Т/g, "T");
  thisValue = thisValue.replace(/У/g, "Y");
  thisValue = thisValue.replace(/Х/g, "X");
  
  let allowCharsGN = ['A', 'B', 'E', 'K', 'M', 'H', 'O', 'P', 'C', 'T', 'Y', 'X'];
  // check allow
  let allowedCharsFromStr = [];
  for (let tjd in thisValue)
  {
    // console.log(thisValue[tjd]);
    let allowThisChar = false;
    for (let gfd in allowCharsGN)
    {
      if (allowCharsGN[gfd] == thisValue[tjd])
      {
        allowThisChar = true;
      }
    }
    if (allowThisChar === true)
    {
      if (charsLimit && charsLimit>0)
      {
        if (allowedCharsFromStr.length<charsLimit)
        {
          allowedCharsFromStr.push(thisValue[tjd]);
        }
      }
      else
      {
        allowedCharsFromStr.push(thisValue[tjd]);
      }
    }
  }
  thisValue = allowedCharsFromStr.join('');

  return thisValue;
}

// gn filter
//$(function() { $("#__aorign_input").mask("a 999 aa 99?9"); });
// $(function() { $("#__aorign_input_one").mask("a"); });
$(function() { $("#__aorign_input_two").mask("999"); });
// $(function() { $("#__aorign_input_three").mask("aa"); });
$(function() { $("#__aorign_input_four").mask("99?9"); });
//$(document).on("keyup keydown keypress change paste",'#__aorign_input', function(e){
  //this.value = this.value.replace(/[^a-zA-Z0-9]/g, "").toUpperCase();
//  this.value = this.value.toUpperCase();
//});
// mileage filter
$(document).on("change paste",'#__aorimileage_input', function(e){
  if (this.value.length > 0)
  {
    this.value = this.value.replace(/[^0-9]/g, "").toUpperCase();
    this.value = aor_number_format(this.value, 0, ',', ' ')+' км';
  }
});
// num filter
$(document).on("keyup keydown keypress change paste",'#__aorphonecheckcode_input', function(e){
  this.value = this.value.replace(/[^0-9]/g, "");
});

$(document).on("keyup keydown keypress change paste",'#__aorign_input_one', function(e){
  this.value = aor_gn_autoreplace_chars(this.value, 1);
});
$(document).on("keyup keydown keypress change paste",'#__aorign_input_three', function(e){
  this.value = aor_gn_autoreplace_chars(this.value, 2);
});

// FUNCTIONS
function selectizeFullClear(inp_selector) // full clear selectize field
{
  var carSelect = $(inp_selector).selectize()[0].selectize;
  carSelect.clear();
  carSelect.clearOptions();
}
function getBrands(selectBrandRef=false) 
{
  $.getJSON('get-info.php?f=brand_top&limit=999999', function(btdata) {
    
    window.aorAutoBrandsTop = btdata;
    
    $.getJSON('get-info.php?f=brand', function(data) {
      
      // add brands sort
      for (var i = 0; i < data.length; i++) 
      {
        let countedItem = aor_getItemByText(window.aorAutoBrandsTop, data[i]["ref"], "ref");
        if (countedItem && countedItem['count'])
        {
          data[i]["count"] = countedItem['count'];
        }
        if (!data[i]["count"]) { data[i]["count"] = 0; }
      }
      
      var carSelect = $('#__aoribrand_select').selectize()[0].selectize;
      
      selectizeFullClear('#__aorimodel_select');
      selectizeFullClear('#__aorigen_select');
      selectizeFullClear('#__aoriseries_select');
      selectizeFullClear('#__aorimod_select');
      
      var html = '';
      var len = data.length;
      for (var i = 0; i< len; i++) 
      {
        carSelect.addOption({value: data[i]["ref"], text: data[i]["name"], count: data[i]["count"]});
      }
        
      // SET value
      if (selectBrandRef) {carSelect.setValue(selectBrandRef);}
      
    }).fail(function() {
      aorAlert("Ошибка загрузки брендов (API не отвечает). Попробуйте, пожалуйста, чуть позже.");
    });
    
  }).fail(function() {
    aorAlert("Ошибка загрузки TOP брендов (API не отвечает). Попробуйте, пожалуйста, чуть позже.");
  });

}
function getModels(selectModelRef=false) 
{
  let brendRef = $("#__aoribrand_select").val();
  if (!brendRef || brendRef.length < 5) return false;
  
  // models top
  $.getJSON('get-info.php?f=model_top&brand_ref='+brendRef+'&limit=999999', function(mtdata) {
    
    window.aorAutoModelsTop = mtdata;
    
    // models
    $.getJSON('get-info.php?f=model&brand_ref='+brendRef, function(data) {
      
      // add models sort
      for (var i = 0; i < data.length; i++) 
      {
        let countedItem = aor_getItemByText(window.aorAutoModelsTop, data[i]["ref"], "ref");
        if (countedItem && countedItem['count'])
        {
          data[i]["count"] = countedItem['count'];
        }
        if (!data[i]["count"]) { data[i]["count"] = 0; }
      }
      // sort
      //data.sort(function(a, b){
      //    var keyA = a.count,
      //        keyB = b.count;
          // Compare the 2 dates
      //    if(keyA < keyB) return 1;
      //    if(keyA > keyB) return -1;
      //    return 0;
      //});
      
      var carSelect = $('#__aorimodel_select').selectize()[0].selectize;
      
      carSelect.clear();
      carSelect.clearOptions(); //clear
      selectizeFullClear('#__aorigen_select');
      selectizeFullClear('#__aoriseries_select');
      selectizeFullClear('#__aorimod_select');
      
      var html = '';
      var len = data.length;
      for (var i = 0; i< len; i++) 
      {
        carSelect.addOption({value: data[i]["ref"], text: data[i]["name"], count: data[i]["count"]});
      }
        
      // SET value
      if (selectModelRef) {carSelect.setValue(selectModelRef);}
      
    }).fail(function() {
      //clear
      aorAlert("Не удалось загрузить модели по марке "+$("#__aoribrand_select").text()+". Попробуйте, пожалуйста, чуть позже.");
      selectizeFullClear('#__aorimodel_select');
    });
  
  }).fail(function() {
    //clear
    aorAlert("Не удалось загрузить TOP моделей по марке "+$("#__aoribrand_select").text()+". Попробуйте, пожалуйста, чуть позже.");
    selectizeFullClear('#__aorimodel_select');
  });
}
function getGeneration(genYear, genType='autoload', selectGenRef=false) 
{
  let modelRef = $("#__aorimodel_select").val();
  if (!modelRef || modelRef.length < 5) return false;
  
  $.getJSON('get-info.php?f=generation&model_ref='+modelRef+'&year='+parseInt(genYear, 10), function(data) {
    
    var carSelect = $('#__aorigen_select').selectize()[0].selectize;
    
    carSelect.clear();
    carSelect.clearOptions(); //clear
    selectizeFullClear('#__aoriseries_select');
    selectizeFullClear('#__aorimod_select');
    
    var html = '';
    var len = data.length;
    for (var i = 0; i< len; i++) 
    {
      var yearsArr = [];
      var startDate = new Date(data[i]["made_before"]).getFullYear();
      var endDate = new Date(data[i]["made_after"]).getFullYear();
      for (var j = startDate; j <= endDate; j++)
      {
        if (j < 1000) {break;} //exit with 1 years
        
        yearsArr.push(j);
        
        // reserv. break
        if (j > startDate+100) {break;}
      }
      
      // "0001-01-01T00:00:00" year fix - set this year
      if (startDate == 1) {yearsArr.push(new Date().getFullYear());}
      
      carSelect.addOption({value: data[i]["ref"], text: data[i]["name"], years: yearsArr});
    }
      
    // SET value
    if (selectGenRef) {carSelect.setItem(selectGenRef);}
    
  }).fail(function() {
    //clear
    if (genType && genType == 'enter')
    {
      aorAlert("Не удалось загрузить поколение по году "+genYear+".");
    }
    else
    {
      aorAlert("Не удалось загрузить поколение по модели "+$("#__aorimodel_select").text()+". Попробуйте, пожалуйста, чуть позже.");
    }
    selectizeFullClear('#__aorigen_select');
  });
}
function getGenerationFS(genYear, genType='autoload', selectGenRef=false) 
{
  let modelRef = $("#__aorimodel_select").val();
  if (!modelRef || modelRef.length < 5) return false;
  
  $.getJSON('get-info.php?f=generation&model_ref='+modelRef+'&year='+parseInt(genYear, 10), function(data) {
    
    var carSelect = $('#__aorigenfs_select').selectize()[0].selectize;
    
    carSelect.clear();
    carSelect.clearOptions(); //clear
    selectizeFullClear('#__aoriseriesfs_select');
    selectizeFullClear('#__aorimodfs_select');
    
    var html = '';
    var len = data.length;
    for (var i = 0; i< len; i++) 
    {
      var yearsArr = [];
      var startDate = new Date(data[i]["made_before"]).getFullYear();
      var endDate = new Date(data[i]["made_after"]).getFullYear();
      for (var j = startDate; j <= endDate; j++)
      {
        if (j < 1000) {break;} //exit with 1 years
        
        yearsArr.push(j);
        
        // reserv. break
        if (j > startDate+100) {break;}
      }
      
      // "0001-01-01T00:00:00" year fix - set this year
      if (startDate == 1) {yearsArr.push(new Date().getFullYear());}
      
      carSelect.addOption({value: data[i]["ref"], text: data[i]["name"], years: yearsArr});
    }
      
    // SET value
    if (selectGenRef) {carSelect.setItem(selectGenRef);}
    
  }).fail(function() {
    //clear
    if (genType && genType == 'enter')
    {
      aorAlert("Не удалось загрузить поколение по году "+genYear+".");
    }
    else
    {
      aorAlert("Не удалось загрузить поколение по модели "+$("#__aorimodelfs_select").text()+". Попробуйте, пожалуйста, чуть позже.");
    }
    selectizeFullClear('#__aorigenfs_select');
  });
}
function getSeries(selectSeriesRef=false) 
{
  let genRef = $("#__aorigen_select").val();
  if (!genRef || genRef.length < 5) return false;
  
  $.getJSON('get-info.php?f=series&generation_ref='+genRef, function(data) {
    
    var carSelect = $('#__aoriseries_select').selectize()[0].selectize;
    
    carSelect.clear();
    carSelect.clearOptions(); //clear
    selectizeFullClear('#__aorimod_select');
    
    var html = '';
    var len = data.length;
    for (var i = 0; i< len; i++) 
    {
      carSelect.addOption({value: data[i]["ref"], text: data[i]["name"]});
    }
      
    // SET value
    if (selectSeriesRef) {carSelect.setItem(selectSeriesRef);}
    
  }).fail(function() {
    //clear
    aorAlert("Не удалось загрузить серии по поколению "+$("#__aorigen_select").text()+". Попробуйте, пожалуйста, чуть позже.");
    selectizeFullClear('#__aoriseries_select');
  });
}

function getSeriesFS(selectSeriesRef=false) 
{
  let genRef = $("#__aorigenfs_select").val();
  if (!genRef || genRef.length < 5) return false;
  
  $.getJSON('get-info.php?f=series&generation_ref='+genRef, function(data) {
    
    var carSelect = $('#__aoriseriesfs_select').selectize()[0].selectize;
    
    carSelect.clear();
    carSelect.clearOptions(); //clear
    selectizeFullClear('#__aorimodfs_select');
    
    var html = '';
    var len = data.length;
    for (var i = 0; i< len; i++) 
    {
      carSelect.addOption({value: data[i]["ref"], text: data[i]["name"]});
    }
      
    // SET value
    if (selectSeriesRef) {carSelect.setItem(selectSeriesRef);}
    
  }).fail(function() {
    //clear
    aorAlert("Не удалось загрузить серии по поколению "+$("#__aorigenfs_select").text()+". Попробуйте, пожалуйста, чуть позже.");
    selectizeFullClear('#__aoriseriesfs_select');
  });
}
function getModification(selectModsRef=false) 
{
  let seriesRef = $("#__aoriseries_select").val();
  if (!seriesRef || seriesRef.length < 5) return false;
  
  $.getJSON('get-info.php?f=modification&series_ref='+seriesRef, function(data) {
    //console.log(data);
    
    var carSelect = $('#__aorimod_select').selectize()[0].selectize;
    
    carSelect.clear();
    carSelect.clearOptions(); //clear
    
    var html = '';
    var len = data.length;
    for (var i = 0; i< len; i++) 
    {
      carSelect.addOption({value: data[i]["ref"], text: data[i]["name"]});
    }
      
    // SET value
    if (selectModsRef) 
    {
      carSelect.setItem(selectModsRef); 
    }
    
  }).fail(function() {
    //clear
    aorAlert("Не удалось загрузить модификации по серии "+$("#__aoriseries_select").text()+". Попробуйте, пожалуйста, чуть позже.");
    selectizeFullClear('#__aorimod_select');
  });
}
function getModificationFS(selectModsRef=false) 
{
  let seriesRef = $("#__aoriseriesfs_select").val();
  if (!seriesRef || seriesRef.length < 5) return false;
  
  $.getJSON('get-info.php?f=modification&series_ref='+seriesRef, function(data) {
    //console.log(data);
    
    var carSelect = $('#__aorimodfs_select').selectize()[0].selectize;
    
    carSelect.clear();
    carSelect.clearOptions(); //clear
    
    var html = '';
    var len = data.length;
    for (var i = 0; i< len; i++) 
    {
      carSelect.addOption({value: data[i]["ref"], text: data[i]["name"]});
    }
      
    // SET value
    if (selectModsRef) 
    {
      carSelect.setItem(selectModsRef); 
    }
    
  }).fail(function() {
    //clear
    aorAlert("Не удалось загрузить модификации по серии "+$("#__aoriseriesfs_select").text()+". Попробуйте, пожалуйста, чуть позже.");
    selectizeFullClear('#__aorimodfs_select');
  });
}
function getCompanies() 
{
  let brandRef = window.aorAuto.brand_ref;
  if (!brandRef || brandRef.length < 5) return false;
  
  $.getJSON('get-info.php?f=companies&brand_ref='+brandRef, function(data) {
    //window.aorCompaniesList = data;
    //console.log(data);
    
    window.aorCompaniesList = {}; // clear
    
    let thisSelectedCompany = $('#__aoridcompany_select').val();
    var carSelect = $('#__aoridcompany_select').selectize()[0].selectize;
    
    carSelect.clear();
    carSelect.clearOptions(); //clear
    
    var html = '';
    var len = data.length;
    for (var i = 0; i< len; i++) 
    {
      if (data[i]["dep_ref"] && data[i]["dep_ref"].length > 10)
      {
        let companyUniqueID = new aorRobot().name;
        //data[i]["dep_ref"]
        carSelect.addOption({value: companyUniqueID, text: data[i]["dep_full_name"], years: data[i]["dep_address"]});
        window.aorCompaniesList[companyUniqueID] = data[i];
      }
    }
    
    // find company and SET value
    if (thisSelectedCompany.length > 10 && window.aorCompany && window.aorCompany.ref && window.aorCompany.ref.length > 5) 
    {
      for (var cmp in window.aorCompaniesList)
      {
        if (window.aorCompaniesList[cmp]['dep_ref'] == window.aorCompany.ref && window.aorCompaniesList[cmp]['dep_full_name'] == window.aorCompany.name) 
        {
          carSelect.setValue(cmp); 
          break; 
        }
      }
    }
    
  }).fail(function() {
    //clear
    aorAlert("Не удалось загрузить сервисы. Попробуйте, пожалуйста, чуть позже.");
    selectizeFullClear('#__aoridcompany_select');
  });
}
// get companies for suggestion TO
function getCompaniesAutomatic() 
{
  let brandRef = window.aorAuto.brand_ref;
  if (!brandRef || brandRef.length < 5) return false;
  
  $.getJSON('get-info.php?f=companies&brand_ref='+brandRef, function(data) {
    //console.log(data);
    
    window.aorCompaniesList = {}; // clear
    window.aorCompaniesList = data; // fill
    
    // > 1 - choose (__aor_button_gotocompanysel)
    if (window.aorCompaniesList && window.aorCompaniesList.length > 1)
    {
      $('.__aor_button_gotocompanysel').click();
    }
    // == 1 - fill company, go next (__aor_button_gotodate)
    else if (window.aorCompaniesList && window.aorCompaniesList.length == 1 && window.aorCompaniesList[0] && window.aorCompaniesList[0]["dep_ref"] && window.aorCompaniesList[0]["dep_ref"].length > 5 && window.aorCompaniesList[0]['dep_full_name'] && window.aorCompaniesList[0]['dep_address'])
    {
      // set
      window.aorCompany.ref = window.aorCompaniesList[0]['dep_ref'];
      window.aorCompany.name = window.aorCompaniesList[0]['dep_full_name'];
      window.aorCompany.address = window.aorCompaniesList[0]['dep_address'];
      // go date 
      $('.__aor_button_gotodate').click();
    }
    // < 1 and etc - err (__aor_button_goerror)
    else //if (window.aorCompaniesList && window.aorCompaniesList.length < 1)
    {
      // ya m goal
      if (window.ym)
      {
        ym(53948962, 'reachGoal', 'suggestionto_err_companynotselected');
      }
      
      // goerror
      // $('.__aor_button_goerror').click();

      // show "services not found"
      $('.__aor_button_servicenotfound').click();
    }
    
  }).fail(function() {
    //clear
    aorAlert("Не удалось загрузить сервисы. Попробуйте, пожалуйста, чуть позже.");
    selectizeFullClear('#__aoridcompany_select');
  });
}
function getPackages(selectPackageRef=false) 
{
  // set loading state
  $("table.__aor_packages_list tbody").html('<tr><td colspan="6" style="text-align: center;">Данные загружаются...</td></tr>');
  
  var carBrand = $('#__aoribrand_select').val();
  var carModel = $('#__aorimodel_select').val();
  var carGen = $('#__aorigen_select').val();
  var carSer = $('#__aoriseries_select').val();
  var carModif = $('#__aorimod_select').val();
  
  // mileage get
  var carMileage = parseInt($('#__aorimileage_input').val().replace(/\D/g,''), 10);
  if (isNaN(carMileage)) {carMileage = 0;} // null fix
  // - 15 000 km (add "late packages")
  if (carMileage >= 15000) {carMileage -= 15000;}
  
  if (carBrand.length < 1 && carModel.length < 1 && carGen.length < 1) 
  {
    aorAlert('Необходимо заполнить как минимум одно из полей: Марка, Модель и/или Поколение'); 
    return false;
  }

  $.getJSON('get-info.php?f=services&brand_ref='+carBrand+'&model_ref='+carModel+'&generation_ref='+window.aorAuto.generation_ref+'&ser_ref='+window.aorAuto.seria_autoru_ref+'&modification_ref='+window.aorAuto.modification_autoru_ref+'&mil='+carMileage, function(data) {
    
    // search filter
    let aorfltr = {};
    if (window.thisfltr_operations && window.thisfltr_operations.length>0) {
      let th_fltr_operations = window.thisfltr_operations.replace(new RegExp( '&quot;' , 'g' ), '"');
      if (aor_isJSON(th_fltr_operations)) {
        aorfltr = JSON.parse( th_fltr_operations );
      }
    }

    let kdata = {};


    var r = new Array(), j = -1;
    for (var key=0, size=data.length; key<size; key++)
    {
       if (aorfltr && aorfltr[0] && aorfltr[0].length>0) { 
          // check in operation filter
          if (!data[key]['def_op_ref'] || data[key]['def_op_ref'].length < 1 || aorfltr.indexOf( (data[key]['def_op_ref']).toLowerCase() ) < 0) {
            continue;
          }
       }

       if (data[key]["versions"] && data[key]["versions"].length > 1)
       {
          // VERSIONS GROUP
          
          let groupIDm = new aorRobot().name;
          
          //group
          r[++j] ='<tr class="__aor_packages_group'+(data[key]["is_error"]==true?' is_error':'')+'" subpackages-id="'+data[key]["ref"]+'" group-id="'+groupIDm+'"><td class="one cb-packs-group-ind">';
          r[++j] = '➕</td><td></td><td class="aor-p-name">';
          r[++j] = data[key]["def_op_name"]; // Название
          r[++j] = '</td><td class="__aor_table_mobilehide">';
          r[++j] = data[key]["mileage"] > 0 ? aor_number_format(data[key]["mileage"], 0, ',', ' ') : '<span style="color:gray;">—</span>'; // Пробег
          r[++j] = '</td><td style="color:gray;" class="__aor_serviceworkhours_place __aor_table_mobilehide">';
          //r[++j] = data[key]["comment"]; // Коммент
          r[++j] = ''; // Часы факт. работ
          r[++j] = '</td><td class="__aor_serviceprice_place __aor_table_mobilehide">';
          r[++j] = ''; //Цена
          r[++j] = '</td></tr>';
          
          for (var vrkey=0, vrsize=data[key]["versions"].length; vrkey<vrsize; vrkey++)
          {
            let milTxt = data[key]["mileage"] > 0 ? aor_number_format(data[key]["mileage"], 0, ',', ' ') : '<span style="color:gray;">—</span>';
            
            //versions
            r[++j] ='<tr class="__aor_packages_item subgroup-item'+(vrkey==(data[key]["versions"].length-1)?' last-item':'')+(vrkey==0?' first-item':'')+'" package-id="'+data[key]["ref"]+'" id="'+data[key]["ref"]+'" version="'+data[key]["versions"][vrkey]["version"]+'" unique-id="'+(new aorRobot().name)+'" group-id="'+groupIDm+'" style="display:none;"><td></td><td class="one cb-packs-ind">';
            r[++j] = '</td><td class="aor-p-name">';
            r[++j] = data[key]["def_op_name"]+' ('+data[key]["versions"][vrkey]["version"]+')'; // Название
            r[++j] = '<span class="__aor_p_name_mil">Пробег: '+milTxt+'</span><span class="__aor_p_name_work">Время выполнения: <span class="__aor_serviceworkhours_place"></span></span><span class="__aor_p_name_price">Цена: <span class="__aor_serviceprice_place"></span></span>'; // mob.
            r[++j] = '</td><td class="__aor_table_mobilehide">';
            r[++j] = milTxt; // Пробег
            r[++j] = '</td><td style="color:gray;" class="__aor_serviceworkhours_place __aor_table_mobilehide">';
            //r[++j] = data[key]["comment"]; // Коммент
            r[++j] = ''; // Часы факт. работ
            r[++j] = '</td><td class="__aor_serviceprice_place __aor_table_mobilehide">';
            r[++j] = ''; //Цена
            r[++j] = '</td></tr>';
          }
       }
       else
       {
          // ALONE PACKAGE
          
          let milTxt = data[key]["mileage"] > 0 ? aor_number_format(data[key]["mileage"], 0, ',', ' ') : '<span style="color:gray;">—</span>';
          
          r[++j] ='<tr class="__aor_packages_item'+(data[key]["ref"].length<5?' empty-item':'')+'" package-id="'+data[key]["ref"]+'" id="'+data[key]["ref"]+'" unique-id="'+(new aorRobot().name)+'" version="'+(data[key]["versions"][0]?data[key]["versions"][0]["version"]:'')+'"><td></td><td class="one cb-packs-ind">';
          //r[++j] = '<input type="checkbox" class="cb-packs-inp">';
          r[++j] = '</td><td class="aor-p-name">'+(data[key]["ref"].length<5?'<span class="__aor_p_voskl">!</span> ':'');
          r[++j] = data[key]["def_op_name"]; // Название
          if (data[key]["ref"].length > 5)
          {
            r[++j] = '<span class="__aor_p_name_mil">Пробег: '+milTxt+'</span><span class="__aor_p_name_work">Время выполнения: <span class="__aor_serviceworkhours_place"></span></span><span class="__aor_p_name_price">Цена: <span class="__aor_serviceprice_place"></span></span>'; // mob.
          }
          r[++j] = '</td><td class="__aor_table_mobilehide">';
          r[++j] = milTxt; // Пробег
          r[++j] = '</td><td style="color:gray;" class="__aor_serviceworkhours_place __aor_table_mobilehide">';
          //r[++j] = data[key]["comment"]; // Коммент
          r[++j] = ''; // Часы факт. работ
          r[++j] = '</td><td class="__aor_serviceprice_place __aor_table_mobilehide">';
          r[++j] = ''; //Цена
          r[++j] = '</td></tr>';
          
       }
       
       if (data[key]["ref"] && data[key]["ref"].length>5)
       {
          // rebuild array on keys
          kdata[data[key]["ref"]] = data[key];
          // add selected indicator
          kdata[data[key]["ref"]]["p_selected"] = 0;
          // "todelete" init
          kdata[data[key]["ref"]]["todelete"] = { "services": {}, "products": {} };
          // add selected version (default value)
          kdata[data[key]["ref"]]["version_selected"] = '';
       }
      
    }
    
    // update global
    window.aorServiceList = kdata;
    
    // empty 
    if (data.length < 1) { r.push('<tr><td colspan="6" style="text-align:center;"><span style="color:gray;text-align:center;">Подходящих пакетов не найдено.</span></td></tr>'); }
    // write
    $("table.__aor_packages_list tbody").html(r.join(''));
    
    // calc price
    calcTotalPackageSumm();
    
  }).fail(function() {
    aorAlert("Ошибка загрузки пакетов. Попробуйте, пожалуйста, чуть позже.");
  });
}
function calcTotalPackageSumm()
{
  let total_summ = 0;
  
  // services + products
  $('.__aor_packages_list tr.__aor_packages_item').each(function( index ) {
    
    var aorpackageID = $(this).attr('package-id');
    var aorsversion = $(this).attr('version');

    if (typeof aorpackageID !== typeof undefined && aorpackageID !== false && typeof aorsversion !== typeof undefined && aorsversion !== false && aorpackageID.length > 0 && aorsversion.length > 0) 
    {
      let thisPackage = window.aorServiceList[aorpackageID];
      let thisPackageVersion = aor_getItemByText(window.aorServiceList[aorpackageID]['versions'], aorsversion, 'version');
      
      let aorServicePriceSumm = 0; // price
      let aorServiceWorkHoursSumm = 0; // work_hours
      
      for(var p in thisPackageVersion['products'])
      {
        aorServicePriceSumm += thisPackageVersion['products'][p]['summ'];
      }
      
      for(var s in thisPackageVersion['services'])
      {
        aorServicePriceSumm += thisPackageVersion['services'][s]['summ'];
        aorServiceWorkHoursSumm += thisPackageVersion['services'][s]['work_hour']*thisPackageVersion['services'][s]['amount'];
      }
      
      //for (var spkey=0, spsize=thisPackageVersion['products'].length; spkey<spsize; spkey++)
      //{
      //  aorServicePriceSumm += thisPackageVersion['products'][spkey]['summ'];
      //}
      
      //for (var sskey=0, sssize=thisPackageVersion['services'].length; sskey<sssize; sskey++)
      //{
      //  aorServicePriceSumm += thisPackageVersion['services'][sskey]['summ'];
      //}
      
      $(this).find('.__aor_serviceprice_place').text(aor_number_format(aorServicePriceSumm, 0, ',', ' ')+' руб.');
      $(this).find('.__aor_serviceworkhours_place').text(aor_number_format(aorServiceWorkHoursSumm, 2, ',', ' ')+' ч.');
    }
    
  });
}
function showAllTimes(satType='time')
{
  if (!window.aorCompany || !window.aorCompany.ref || window.aorCompany.ref.length < 5) { return false; }
  if (!window.aorDateTime || !window.aorDateTime.date || window.aorDateTime.date.length < 5) { return false; }
  
  let addDTURL = '';
  // akr record
  if (window.jWorkParts && window.jWorkParts.akr_data && window.jWorkParts.akr_data.manager_ref)
  {
    addDTURL += '&adviser='+window.jWorkParts.akr_data.manager_ref;
  }
  // akr rerecord
  if (window.jRRInfo && window.jRRInfo.m_ref)
  {
    addDTURL += '&adviser='+window.jRRInfo.m_ref;
  }

  // loading...
  let writeTimeObjN =  $('.__aor_c_inner_at_div');
  if (satType == 'retime') { writeTimeObjN = $('.__aor_c_inner_re_at_div'); }
  writeTimeObjN.html('<div class="__aor_at_emptyresult"><br />Информация обновляется. Пожалуйста, подождите немного.</div>');
  
  // get query and print
  $.getJSON('get-info.php?f=datetime&dep='+window.aorCompany.ref+'&date='+window.aorDateTime.date+addDTURL, function(atdata) {
    
    //console.log(atdata);
    
    let writeTimeObj =  $('.__aor_c_inner_at_div');
    if (satType == 'retime') { writeTimeObj = $('.__aor_c_inner_re_at_div'); }
    
    if (atdata && atdata['slots'] && atdata['advisers'] && atdata['slots'].length > 0)
    {
      let atFinalString = '';
      // save advisers and slots
      window.aorAdvisers = atdata['advisers'];
      window.aorSlots = atdata['slots'];
      window.aorSlotsGroup = {}; // clear!
      //calc workload
      window.aorAdviserStat = {};
      for (var av in atdata['advisers'])
      {
        let adviserTotalTimes = 0;
        let adviserBusyTimes = 0;
        for (var sl in atdata['slots'])
        {
          if (atdata['slots'][sl]['adviser'] == av) // only this adviser
          {
            adviserTotalTimes++;
            if (!atdata['slots'][sl]['available']) //busy
            {
              adviserBusyTimes++;
            }
          }
        }
        // calc
        let adviserWorkload = 0;
        if (adviserTotalTimes > 0 && adviserBusyTimes > 0)
        {
          adviserWorkload = (adviserBusyTimes/adviserTotalTimes)*100;
        }
        
        // set
        window.aorAdviserStat[av] = {'total_times': adviserTotalTimes, 'busy_times': adviserBusyTimes, 'workload_percentage': adviserWorkload};
      }
      
      // slots groups
      for (var sp in atdata['slots'])
      {
        // search "late time"
        if (checkStartDate_isLate(atdata['slots'][sp]['start'])) { continue; }
        
        let slotGroupName = printHoursFromISO(atdata['slots'][sp]['start'])+' - '+printHoursFromISO(atdata['slots'][sp]['end']);
        
        if (!window.aorSlotsGroup[slotGroupName]) // not isset? create
        {
          window.aorSlotsGroup[slotGroupName] = {};
        }
        
        window.aorSlotsGroup[slotGroupName][atdata['slots'][sp]['adviser']] = atdata['slots'][sp];
      }
      
      // print slots (old all listing)
      //for (var p in atdata['slots'])
      //{
      //  atFinalString += '<div class="__aor_at_timeitem'+(!atdata['slots'][p]['available']?' __aor_notavaliable_time':'')+'" start="'+atdata['slots'][p]['start']+'" end="'+atdata['slots'][p]['end']+'" adviser="'+atdata['slots'][p]['adviser']+'">'+printHoursFromISO(atdata['slots'][p]['start'])+' - '+printHoursFromISO(atdata['slots'][p]['end'])+'<br />'+atdata['advisers'][atdata['slots'][p]['adviser']]+'</div>';
      //}
      //atFinalString += '<div style="float:none;clear:both;"></div><br /><br />';
      //$('.__aor_c_inner_at_div').html(atFinalString+'<div style="float:none;clear:both;"></div>');
      
      // print slots (select min workload adviser)
      for (var p in aorSlotsGroup)
      {
        // balance system
        // ==1 -> print, >1 -> workload
        let thisPrintedSlot = {};
        if (aorSlotsGroup[p].length < 2)
        {
          thisPrintedSlot = aor_first(aorSlotsGroup[p]);
        }
        else // >= 2
        {
          let minWorkLoad = 100; // last minimal workload value (default 100%)
          for (var gadviser in aorSlotsGroup[p])
          {
            if (window.aorAdviserStat[gadviser]['workload_percentage'] < minWorkLoad && aorSlotsGroup[p][gadviser]['available']) 
            { 
              thisPrintedSlot = aorSlotsGroup[p][gadviser];
              minWorkLoad = window.aorAdviserStat[gadviser]['workload_percentage'];
            }
          }
        }
        
        // print
        atFinalString += '<div class="__aor_at_timeitem'+(!thisPrintedSlot['available']?' __aor_notavaliable_time':'')+'" start="'+thisPrintedSlot['start']+'" end="'+thisPrintedSlot['end']+'" adviser="'+thisPrintedSlot['adviser']+'">'+p+'<span class="__aor_at_timeitem_adviser_name"><br />'+atdata['advisers'][thisPrintedSlot['adviser']]+'</span></div>';
      }
      // float clear
      atFinalString += '<div style="float:none;clear:both;"></div>';
      
      writeTimeObj.html(atFinalString);
    }
    else
    {
      writeTimeObj.html('<div class="__aor_at_emptyresult"><br />Доступного времени не найдено. <br /><br />Обязательно свяжитесь с нами по номеру <b>8 (3452) 29-00-00</b>, запишитесь через оператора и расскажите, что именно у Вас не получилось при онлайн записи. <br />Спасибо, будем ждать Вас!</div>');
    }
    
  }).fail(function() {
    aorAlert("Ошибка загрузки доступного времени на "+window.aorDateTime.date.replace(/-/g, '.')+". Попробуйте, пожалуйста, чуть позже.");
  });
  
}
function printAllRecordData()
{
  // print auto
  $(".aorfinalcarname_text").text(window.aorAuto.name.replace('&#039;', "'"));
  
  // print service
  $(".aorfinalcompanyname").text(window.aorCompany.name);
  $(".aorfinalcompanyaddress").text(window.aorCompany.address);
  $(".__aor_print_company").text(window.aorCompany.name);
  $(".__aor_print_address").text(window.aorCompany.address);
  
  // print date time
  $(".aorfinalrdate_text").text(window.aorDateTime.date.replace(/-/g, '.')+', '+printHoursFromISO(window.aorDateTime.start)+' - '+printHoursFromISO(window.aorDateTime.end));
  $(".__aor_print_date").text(window.aorDateTime.date.replace(/-/g, '.'));
  $(".__aor_print_time").text(printHoursFromISO(window.aorDateTime.start));
  
  // print pd info
  $(".aorfinalyoupd_text").text(window.aorPersonal.lastname+' '+window.aorPersonal.firstname+', '+window.aorPersonal.phone);
  
}
function aorsmsStep()
{
  // show phone
  $('.aorphonecheckinfo_phone').text(window.aorPersonal.phone);
  
  // h/sh default
  $('.aorphonecheckinfo_item').hide();
  $('.aorphonecheckinfo_rq').show();
  
  // send
  $.getJSON('sms.php?f=record&rid='+window.aorInfo['rid']+'&timestamp='+window.aorInfo['timestamp']+'&phone='+(window.aorPersonal['phone'].replace(/\D/g,'')), function(smsdata) {
    
    if (smsdata && smsdata.status && smsdata.status == "ok")
    {
      // change aorphonecheckinfo_item
      $('.aorphonecheckinfo_item').hide();
      $('.aorphonecheckinfo_smssended').show();
    }
    else if (smsdata && smsdata.status && smsdata.info && smsdata.status == "error" && smsdata.info == "ip_limit_exceeded")
    {
      alert('С Вашего IP адреса слишком много одновременных запросов. Попробуйте, пожалуйста, повторить немного позже.');
      // clickedButton.addClass('nblock');
      // unweel();

      // show "buttonplaces"
      // $('.alkphonecheckinfo_resend_waitstep').hide();
      // $('.alkphonecheckinfo_resend_truestep').show();

      // ya m goal
      if (window.ym)
      {
        ym(53948962, 'reachGoal', 'booking_ipexceeded');
      }
    }
    else
    {
      //$('.__aor_button_goerror').click();
      
      // err
      $('.aorphonecheckinfo_item').hide();
      $('.aorphonecheckinfo_error').show();
      
      // ya m goals
      if (window.ym)
      {
        ym(53948962, 'reachGoal', 'sms_error');
      }
      
    }
    
    // show resend & start timer
    aorStartSMSresendTimer();
    
  }).fail(function() {
    //$('.__aor_button_goerror').click();
      
    // err
    $('.aorphonecheckinfo_item').hide();
    $('.aorphonecheckinfo_error').show();
    
    // ya m goals
    if (window.ym)
    {
      ym(53948962, 'reachGoal', 'sms_error');
    }
    
  });
  
}
function aorStartSMSresendTimer()
{
  window.aorSMSSendDate = new Date();
  window.aorSMStimeleftTimer = setInterval(resetReSMSTimeleft, 1000);
}
function resetReSMSTimeleft() { 
  let lastSendDate = window.aorSMSSendDate; // lastSendDate is date obj
  let aorPDate = new Date(new Date().getTime() - 2*60000); // +2 min
  var totalSecondsDiff = (lastSendDate.getTime() - aorPDate.getTime()) / 1000;
  $(".aorphonecheckinfo_resend_timer").text(aorSecondsToHms(totalSecondsDiff));

  if (totalSecondsDiff > 0)
  {
    // show timer
    $('.aorphonecheckinfo_resend_waitstep').show();
    $('.aorphonecheckinfo_resend_truestep').hide();
  }
  else
  {
    // show resend button
    $('.aorphonecheckinfo_resend_truestep').show();
    $('.aorphonecheckinfo_resend_waitstep').hide();
    // stop interval
    clearInterval(window.aorSMStimeleftTimer);
  }
}
function aorSecondsToHms(d) {
    d = Number(d);
    var m = Math.floor(d % 3600 / 60);
    var s = Math.floor(d % 3600 % 60);
    var mDisplay = m > 0 ? m + (":") : 0 + (":"); if (m < 10) { mDisplay = '0'+mDisplay; }
    var sDisplay = s > 0 ? s : 0; if (s < 10) { sDisplay = '0'+sDisplay; }
    return mDisplay + sDisplay; 
}
function openAOR(params={}, callback)
{
  // clear data
  window.aorInfo = {}; window.aorAuto = {}; window.aorCompany = {}; window.aorCompaniesList = {}; window.aorService = {}; window.aorServiceList = {}; window.aorDateTime = {}; window.aorPersonal = {};
  selectizeFullClear('#__aoribrand_select'); //need clear brand
  // record uid, time
  window.aorInfo['rid'] = (new aorRobot().name);
  window.aorInfo['timestamp'] = + new Date();
  // load brands
  getBrands();
  // set first window
  $(".__aor_c").hide();
  $(".__aor_c.aorcar").show();
  // set title
  $(".__aor_stepname").text($(".__aor_c.aorcar .__aor_c_title_h").text());
  // reset steps
  $(".__aor_numbers_item").removeClass("__aor_ni_active");
  $(".__aor_numbers_item.__aor_numbers_i_one").addClass("__aor_ni_active");
  $(".__aor_numbers").show();
  // show
  $(".__agrad_onlinerecord_overlay").fadeIn('fast', function () {
    $(".__agrad_onlinerecord_inner").fadeIn('slow', function () {
      if (callback)
      {
        callback();
      }
    });
  });
}
function aorAlert(alertMsg, alertMode='')
{
  // it's message
  $(".__aor-window-i-message").show();
  $(".__aor-window-close").show();
  // add
  $(".__aor-window-i-message").text(""); // clear
  if (alertMsg && alertMsg.length > 0) 
  { 
    if (alertMode == 'html')
    {
      $(".__aor-window-block").addClass("aorwbflex")
      $(".__aor-window-i-message").html(alertMsg); 
    }
    else
    {
      $(".__aor-window-block").removeClass("aorwbflex")
      $(".__aor-window-i-message").text(alertMsg); 
    }
  }
  
  // show
  $(".__aor-window-overlay").fadeIn("fast", function () {
    $(".__aor-window-inner").fadeIn("fast");
  });
}
// custom d.t. close action
$(document).on("click", ".__aor-window-overlay, .__aor-window-close", function(e){
  // animate close
  $(".__aor-window-inner").fadeOut("fast", function () {
    $(".__aor-window-overlay").fadeOut("fast");
  });
});

// libs
function aor_number_format(number, decimals, dec_point, separator ) {
  number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof separator === 'undefined') ? ',' : separator ,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function(n, prec) {
      var k = Math.pow(10, prec);
      return '' + (Math.round(n * k) / k)
        .toFixed(prec);
    };
  // Фиксим баг в IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
    .split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '')
    .length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1)
      .join('0');
  }
  return s.join(dec);
}
// generate unique id
function aorRobot(){
 this.name = aorRobot.makeId();
}
aorRobot.nums   = Array.from({length:10},(_,i) => i);
aorRobot.chars  = Array.from({length:26},(_,i) => String.fromCharCode(65+i));
aorRobot.idmap  = {};
aorRobot.makeId = function(){
                 var text = Array.from({length:12}, _ => aorRobot.chars[~~(Math.random()*26)]).join("") +
                            Array.from({length:10}, _ => aorRobot.nums[~~(Math.random()*10)]).join("");
                 return !aorRobot.idmap[text] ? (aorRobot.idmap[text] = true,text) : aorRobot.makeId();
};
function aor_getItemByText(arr, text, subkey='') 
{
  for(var d in arr)
  {
    if ((subkey && subkey.length > 0 && arr[d][subkey] == text) || (arr[d] == text))
    {
      return arr[d];
      break;
    }
  }
  return false;
}
function printHoursFromISO(iso_date) 
{
  if (iso_date == '0001-01-01T00:00:00') { return ''; }
  
  let tdt = new Date(iso_date+'+05:00');
  tdt = aor_nineFix(tdt); // ninefix
  return ('0' + tdt.getHours()).slice(-2)+":"+('0' + tdt.getMinutes()).slice(-2);
}
function checkStartDate_isLate(iso_date) 
{
  if (iso_date == '0001-01-01T00:00:00') { return false; }
  
  let nowDate = new Date();
  let ttdt = new Date(iso_date+'+05:00');
  ttdt = aor_nineFix(ttdt); // ninefix
  
  if ( ( ttdt.getTime() < nowDate.getTime() ) 
      && ttdt.getDate() == nowDate.getDate() 
      && ttdt.getMonth() == nowDate.getMonth() 
      && ttdt.getFullYear() == nowDate.getFullYear())
    return true;
  else
    return false;
  
}
function aor_printDateFromObj(obj_date) {
  let tdt = new Date(obj_date);
  return ('0' + tdt.getDate()).slice(-2)+"-"+('0' + (tdt.getMonth()+1)).slice(-2)+"-"+tdt.getFullYear();
}
function aor_nineFix(time) // obj Date
{
  if (parseInt(time.getMinutes().toString().slice(-1), 10) == 9) // last minute char is nine
  {
    time = new Date(time.getTime() + 1*60000);
  }
  return time;
}
function aor_zeroAdd(num) // num is int
{
  thisNum = parseInt(num, 10);
  if (thisNum < 10) { thisNum = '0'+thisNum; }
  return thisNum;
}
function aor_isJSON(something) {
    if (typeof something != 'string')
        something = JSON.stringify(something);

    try {
        JSON.parse(something);
        return true;
    } catch (e) {
        return false;
    }
}
// get first element in array
function aor_first(p){for(var i in p)return p[i];}


window.addEventListener('message', infoPMReceive, false);

function infoPMReceive(e) {
  
   if (e.origin == 'https://lk.agrad.ru' || e.origin == 'https://test2.agrad.ru:3333') 
   {
      thisPMdata = {};
      if (e.data && e.data.length > 0 && aor_isJSON(e.data)) { thisPMdata = JSON.parse(e.data); }

      if (thisPMdata.postData)
      {
        
        // basket record
        if ($('.__aor_frombasket_basketcomposition').length < 1 && thisPMdata.postData.basket_composition)
        {
          $('body').append('<div class="__aor_frombasket_basketcomposition" style="display:none;" />');
          $('.__aor_frombasket_basketcomposition').text(thisPMdata.postData.basket_composition);
        }
        if ($('.__aor_frombasket_clientinfo').length < 1 && thisPMdata.postData.basket_composition && thisPMdata.postData.client_info)
        {
          $('body').append('<div class="__aor_frombasket_clientinfo" style="display:none;" />');
          $('.__aor_frombasket_clientinfo').text(thisPMdata.postData.client_info);
        }

        // rerecord 
        if ($('.__aor_fromlk_rerecordinfo').length < 1 && thisPMdata.postData.rerecord_data)
        {
          $('body').append('<div class="__aor_fromlk_rerecordinfo" style="display:none;" />');
          $('.__aor_fromlk_rerecordinfo').text(thisPMdata.postData.rerecord_data);
        }
        if ($('.__aor_fromlk_clientinfo').length < 1 && thisPMdata.postData.rerecord_data && thisPMdata.postData.client_info)
        {
          $('body').append('<div class="__aor_fromlk_clientinfo" style="display:none;" />');
          $('.__aor_fromlk_clientinfo').text(thisPMdata.postData.client_info);
        }

      }

      console.log(e.data);
   } 
   // console.log(e.origin);
}