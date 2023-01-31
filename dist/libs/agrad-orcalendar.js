// Copyright (c) 2002 Yura Ladik http://www.javaportal.ru All rights reserved.
// Permission given to use the script provided that this notice remains as is.

//Функция возвращает текущее время в виде строки
function aor_getTime()
{
 //Инициализируем переменные с параметрами текущего времени
 var now = new Date();
 var hour = now.getHours();
 var minute = now.getMinutes();
 now = null;
 var ampm = "";
 //Устанавливаем значение часа и am pm
 if (hour >= 12)
 {
  hour -= 12;
  ampm = "PM";
 }
 else ampm = "AM";

 hour = (hour == 0) ? 12 : hour;
 //Добавляем нулевую цифру к одной цифре минуты
 if (minute < 10) minute = "0" + minute;
 // Возвращаем строку
 return hour + ":" + minute + " " + ampm
}

//Функция проверки на высокосный год
function aor_isLeapYear(year){ return (year % 4 == 0)}

//Функция возвращает колличество дней в месяце в зависимости от года
function aor_getDays(month, year)
{
 // Создаем массив, для хранения числа дней в каждом месяце
 var ar = new Array(12);
 ar[0] = 31; // Январь
 ar[1] = (aor_isLeapYear(year)) ? 29 : 28 ;// Февраль
 ar[2] = 31; // Март
 ar[3] = 30; // Апрель
 ar[4] = 31; // Май
 ar[5] = 30; // Июнь
 ar[6] = 31; // Июль
 ar[7] = 31; // Август
 ar[8] = 30; // Сентябрь
 ar[9] = 31; // Остябрь
 ar[10] = 30; // Ноябрь
 ar[11] = 31; // Декабрь
 return ar[month]
}

//Функция возвращает название месяца
function aor_getMonthName(month,nameMonth)
{
 // Создаем массив, для хранения названия каждого месяца
 var ar = new Array(12);
if (nameMonth=="rus"||nameMonth=="russ"||nameMonth=="russs")
{
 ar = ["Январь", "Февраль", "Март", "Апрель","Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"];
}else
{
 ar = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
}
 return ar[month]
}

// Функция установки настроек календаря с последующей прорисовкой
function aor_setCalendar(setmonth=-1, setyear=-1)
{

 // Параметры настройки таблицы
 var tableBgColor = "#ffffff"; //Цвет фона таблицы
 var headerHeight = 10; // Высота ячеки заголовка с названием месяца
 var border = 0; // Рамка
 var cellspacing = "0"; // Промежуток между ячейками
 var cellpadding = "0"; // Свободное пространство между содержимым ячейки и её границами

 var headerColor = "#333"; // Цвет текста заголовка в ячейке
 var headerBgColor = "#EEEEEE"; // Цвет фона в ячейке заголовка
 var headerSize = "2"; // Размер шрифта заголовка
 var headerBold= false; // Полужирный шрифта заголовка

 var colWidth = 20; // Ширина столбцов в таблице

 var dayCellHeight = 15; // Высота ячеек содержащих дни недели
 var dayColor = "#356fa1"; // Цвет шрифта, представляющего дни недели
 var dayBgColor = "#ffffff"; // Цвет фона ячеек содержащих дни недели
 var dayBold= false; //Размер шрифта, представляющего дни недели
 var daySize = 2; // Полужирный шрифт представляющий дни недели

 var cellHeight = 10; // Высота ячеек, представляющих даты в календаре

 var todayColor = "#000000"; // Цвет, определяющий сегодняшнюю дату в календаре
 var todayBgColor = "#ddd"; // Цвет фона ячейки с сегодняшней датой
 var todayBold = true; // Полужирный шрифт представляющий сегодняшнюю дату в календаре
 var todaySize = 2; //Размер шрифта, представляющего сегодняшнюю дату в календаре

 var allDayColor = "#333"; // Цвет, остальных дней в календаре
 var allDayBgColor = "#bed2e3"; //Цвет фона остальных ячеек
 var allDayBold= false; // Полужирный шрифт представляющий остальные дни
 var allDaySize= 2; //Размер шрифта, представляющего остальные дни

 var timeColor = "#000000"; // Цвет выводимого времени
 var timeSize = "2"; //Размер шрифта выводимого времени
 var timeBold = false; // Полужирный шрифт выводимого времени
 var isTime = false; //Выводить время или нет
 var nameMonth="russ"; // rus, russ, russs, eng, engs, engss
 aor_drawCalendar( tableBgColor, headerHeight, border,
  cellspacing, cellpadding,
  headerColor, headerBgColor,
  headerSize,  headerBold,
  colWidth,
  dayCellHeight, dayColor, dayBgColor, dayBold, daySize,
  cellHeight,
  todayColor, todayBgColor, todayBold, todaySize,
  allDayColor, allDayBgColor, allDayBold, allDaySize,
  timeColor, timeSize, timeBold, isTime, nameMonth, setmonth, setyear)
}

// 3 months draw
function aor_drawMonths() {
 var year = parseInt($('.__aor-s-hide-date-y').text(), 10); //get from php
 var nextYear = year+1;
 //var now = new Date();
 //var month = now.getMonth();
 var month = parseInt($('.__aor-s-hide-date-m').text(), 10)-1; //get from php
 var monthName = aor_getMonthName(month, "russ");
 var monthNext = month+1;
 var monthNextName = aor_getMonthName(monthNext, "russ");
 var monthNextNext = monthNext+1;
 var monthNextNextName = aor_getMonthName(monthNextNext, "russ");
 let mounthStrHTML = '';
 mounthStrHTML += '<div class="__aor_selmonth_div monthselected" smonth="'+month+'" syear="'+year+'">'+monthName+'</div>';
 
 if (monthNext < 12) 
 {
    mounthStrHTML += '<div class="__aor_selmonth_div" smonth="'+monthNext+'" syear="'+year+'">'+monthNextName+'</div>';
 }
 else
 {
    mounthStrHTML += '<div class="__aor_selmonth_div" smonth="'+(monthNext-12)+'" syear="'+nextYear+'">'+aor_getMonthName(monthNext-12, "russ")+'</div>';
 }

 if (monthNextNext < 12) 
 {
    mounthStrHTML += '<div class="__aor_selmonth_div" smonth="'+monthNextNext+'" syear="'+year+'">'+monthNextNextName+'</div>';
 }
 else
 {
    mounthStrHTML += '<div class="__aor_selmonth_div" smonth="'+(monthNextNext-12)+'" syear="'+nextYear+'">'+aor_getMonthName(monthNextNext-12, "russ")+'</div>';
 }

 mounthStrHTML = '<div class="__aor_selmonth_wrapper">'+mounthStrHTML+'<div style="float:none;clear:both;"></div></div>';
 $('.__aor_c_inner_month .__aor_c_inner_md_div').html(mounthStrHTML);
}

function aor_plusZero(psVar) {
  if (psVar < 10) return '0'+psVar;
  else return psVar;
}

// Функция рисования календаря
function aor_drawCalendar( tableBgColor, headerHeight, border,
 cellspacing, cellpadding,
 headerColor, headerBgColor, headerSize, headerBold,
 colWidth,
 dayCellHeight, dayColor, dayBgColor, dayBold, daySize,
 cellHeight,
 todayColor, todayBgColor, todayBold, todaySize,
 allDayColor, allDayBgColor, allDayBold, allDaySize,
 timeColor, timeSize, timeBold, isTime, nameMonth, setmonth, setyear)
{
 // Переменные
 //var now = new Date();
 //var year = now.getFullYear();
 //var month = now.getMonth();
 
 var year = parseInt($('.__aor-s-hide-date-y').text(), 10); //get from php
 var month = parseInt($('.__aor-s-hide-date-m').text(), 10)-1; //get from php
 var date = parseInt($('.__aor-s-hide-date-d').text(), 10); //get from php
 
 if (setyear && setyear >= 0) { year = parseInt(setyear, 10); }
 if (setmonth && setmonth >= 0) { month = parseInt(setmonth, 10); }
 var monthName = aor_getMonthName(month, nameMonth);
 //var date = now.getDate();
 now = null;
 var firstDayInstance = new Date(year, month, 1);
 var firstDay = firstDayInstance.getDay()+8;
 firstDayInstance = null;
 
 // Число дней в текущем месяце
 var lastDate= aor_getDays(month, year);
 // Создаем основную структуру таблицы
 var text = "";
 text += '<table border=' + border + ' cellspacing=' + cellspacing +
    ' cellpadding='+cellpadding+' bgcolor='+tableBgColor+'>' +
    '<th class="__aor_ctitle_td" colspan=7 height=' + headerHeight +' bgcolor='+headerBgColor+ '>' +
    '<font color="' + headerColor + '" size=' + headerSize + '>';
 if(headerBold) text+='<b>';
 text += monthName + ' ' + year;
 if(headerBold) text+='</b>';
 text += '</font>';
 text += '</th>';
 var openCol = '<td class="__aor_wname_td" width=' + colWidth + ' height=' + dayCellHeight + ' bgcolor='+
  dayBgColor+'>';
 openCol+='<font color="' + dayColor + '" size='+daySize+'>';
 if(dayBold) openCol+='<b>';
 var closeCol = '</font></td>';
 if(dayBold) closeCol='</b>'+closeCol;
 // Создаем массив сокращенных названий дней недели
 var weekDay = new Array(7);
 if(nameMonth=="rus")
 {
  weekDay = ["Пн","ВТ","Ср","Чт","Пт","Сб","Вс"];
 }
 else if(nameMonth=="russ")
 {
  weekDay = ["пн","вт", "ср", "чт", "пт", "сб", "вс"];
 }
 else if(nameMonth=="russs")
 {
  weekDay = ["п", "в", "с", "ч", "п", "с", "в"];
 }
 else if(nameMonth=="eng")
 {
  weekDay = ["Mon", "Tues", "Wed", "Thu", "Fri", "Sat", "Sun"]
 }
 else if(nameMonth=="engs")
 {
  weekDay = ["Mo", "Tu", "We", "Th", "Fr", "Sa", "Su"];
 }
 else if(nameMonth=="engss")
 {
  weekDay = ["m", "t", "w", "t", "f", "s", "s"];
 }
 text += '<tr align="center" valign="center">';
 for (var dayNum = 0; dayNum < 7; ++dayNum)
 {
  text += openCol + weekDay[dayNum] + closeCol
 }
 text += '</tr>';
 var digit = 1;
 var curCell = 2;
 for (var row = 1; row <= Math.ceil((lastDate + firstDay - 1) / 7); ++row)
 {
  text += '<tr align="right" valign="top">';
  for (var col = 1; col <= 7; ++col)
  {
   if (digit > lastDate) break;
   if (curCell < firstDay)
   {
    text += '<td><font size='+allDaySize+' color='+allDayColor+
     '> </font></td>';
    curCell++
   }
   else
   {
    if (digit == date && month == (parseInt($('.__aor-s-hide-date-m').text(), 10)-1) )
    { // Текущая ячейка представляет сегодняшнюю дату
     text += '<td class="__aor_day_td" bgcolor='+todayBgColor+' title="Сегодняшнее число" sdate="'+aor_plusZero(digit)+'-'+aor_plusZero(month+1)+'-'+year+'">';
     text += '<font color="' + todayColor + '" size='+todaySize+'>';
     if(todayBold)text +='<b>';
     text += digit;
     if(todayBold)text +='</b>';
     text += '</font>';
     //Вывод времени
     if(isTime)
     {
      text += '<br>';
      text += '<font color="' + timeColor + '" size='+timeSize+'>';
      text += '<center>';
      if(timeBold)text +='<b>';
      text += aor_getTime();
      if(timeBold)text +='</b>';
      text += '</center>';
      text += '</font>'
     }
     text += '</td>'
    }
    else
    {
     text += '<td class="__aor_day_td'+(digit < date && month == new Date().getMonth()?' __aor_past_day':'')+'" bgcolor='+allDayBgColor+
        ' sdate="'+aor_plusZero(digit)+'-'+aor_plusZero(month+1)+'-'+year+'"><font size='+allDaySize+' color='+allDayColor+'>';
     if(allDayBold)text +='<b>';
     text +=digit;
     if(allDayBold)text +='</b>';
     text +='</font></td>'
    }
    digit++
   }
  }
  text += '</tr>';
 }
 text += '</table>';
 // Выводим полученную строку
 $('.__aor_c_inner_day .__aor_c_inner_md_div').html(text);
 
 // reset active date
 if (window.aorDateTime && window.aorDateTime.date && window.aorDateTime.date.length > 5)
 {
   $('.__aor_day_td').each(function( index ) {
     let thsDate = $(this).attr('sdate');
     if (thsDate == window.aorDateTime.date) { $(this).addClass('dateselected'); return false; }
   });
 }
 
}