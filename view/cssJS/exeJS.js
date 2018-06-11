
$(document).ready(function(){

    $("#addNewEngExe").click(function () {
        engSettingsChange(2);
        $("#showAddInfo").css('display','block');
    });

    $("#breakRepeatWordsExeEng").click(function () {
        sendPost('index.php?exeEng', 'breakRepeatWordsExeEng', '1', 'breakRepeatWordsExeEng');
    });

    $("#resetAllWordsExeEng").click(function () {
        sendPost('index.php?exeEng', 'resetAllWordsExeEng', '1', 'resetAllWordsExeEng');
    });

    $("#resetTodayWordsExeEng").click(function () {
        sendPost('index.php?exeEng', 'resetTodayWordsExeEng', '1', 'resetTodayWordsExeEng');
    });

    $("#startTodayExeEng").click(function () {
        sendPost('index.php?exeEng', 'startTodayExeEng', $("#countExe").val(), 'startTodayExeEng');
    });

    //exeDEU******************

    $("#addNewDeuExe").click(function () {
        engSettingsChange(2);
        $("#showAddInfo").css('display','block');
    });

    $("#breakRepeatWordsExeDeu").click(function () {
        sendPost('index.php?exeDeu', 'breakRepeatWordsExeDeu', '1', 'breakRepeatWordsExeDeu');
    });

    $("#resetAllWordsExeDeu").click(function () {
        sendPost('index.php?exeDeu', 'resetAllWordsExeDeu', '1', 'resetAllWordsExeDeu');
    });

    $("#resetTodayWordsExeDeu").click(function () {
        sendPost('index.php?exeDeu', 'resetTodayWordsExeDeu', '1', 'resetTodayWordsExeDeu');
    });

    $("#startTodayExeDeu").click(function () {
        sendPost('index.php?exeDeu', 'startTodayExeDeu', $("#countExe").val(), 'startTodayExeDeu');
    });


    //exeJava******************

    $("#addNewJavaExe").click(function () {
        engSettingsChange(2);
        $("#showAddInfo").css('display','block');
    });

    $("#breakRepeatWordsExeJava").click(function () {
        sendPost('index.php?exeJava', 'breakRepeatWordsExeJava', '1', 'breakRepeatWordsExeJava');
    });

    $("#resetAllWordsExeJava").click(function () {
        sendPost('index.php?exeJava', 'resetAllWordsExeJava', '1', 'resetAllWordsExeJava');
    });

    $("#resetTodayWordsExeJava").click(function () {
        sendPost('index.php?exeJava', 'resetTodayWordsExeJava', '1', 'resetTodayWordsExeJava');
    });

    $("#startTodayExeJava").click(function () {
        sendPost('index.php?exeJava', 'startTodayExeJava', $("#countExe").val(), 'startTodayExeJava');
    });

    //IR VERBS ENG

    $("#resetAllVerbsEng").click(function () {
        sendPost('index.php?irVerbsEng', 'resetAllVerbsEng', '1', 'resetAllVerbsEng');
    });

    //IR VERBS DEU

    $("#resetAllVerbsDeu").click(function () {
        sendPost('index.php?irVerbsDeu', 'resetAllVerbsDeu', '1', 'resetAllVerbsDeu');
    });
});


function editExe(name, id, inputName){
    var href = $(location).attr('href');
    href = href.replace('&show','');
    if(editClicked === 0) {
        editClicked = 1;
        var text = $(name).text();
        text=text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g,'&apos;');
        $(name).html("<form action='"+href+"' method='post'>" +
            "<input name='"+inputName+"' type='text' maxlength='3000' size='20' value='" + text + "'>" +
            "<input type='hidden' name='"+inputName+"Id' value='"+id+"'></form>");
    }
}

function editWorking(name, id, inputName){
    var href = $(location).attr('href');
    href = href.replace('&show','');
    if(editClicked === 0) {
        editClicked = 1;
        $(name).html("<form action='"+href+"' method='post'>" +
            "<select name='"+inputName+"'>" +
            "<option value='1'>First->Second</option>" +
            "<option value='2'>Second->First</option>" +
            "<option value='3'>W obie strony</option>" +
            "</select>" +
            "<input type='hidden' name='"+inputName+"Id' value='"+id+"'>" +
            "<input type='submit' value='+'></form>");
    }
}

function editMode(name, id, inputName){
    var href = $(location).attr('href');
    href = href.replace('&show','');
    if(editClicked === 0) {
        editClicked = 1;
        $(name).html("<form action='"+href+"' method='post'>" +
            "<select name='"+inputName+"'>" +
            "<option value='1'>Wpisywanie całego pola</option>" +
            "<option value='2'>Wybór/wpisanie słowa</option>" +
            "</select>" +
            "<input type='hidden' name='"+inputName+"Id' value='"+id+"'>" +
            "<input type='submit' value='+'></form>");
    }
}

function editExeName(name, id, inputName, jsonExeNames){
    var href = $(location).attr('href');
    href = href.replace('&show','');
    if(editClicked === 0) {
        editClicked = 1;

        var options = "";
        jsonExeNames.forEach(function(exeName) {
            options += "<option value='"+exeName.id+"'>"+exeName.name+"</option>";
        });

        $(name).html("<form action='"+href+"' method='post'>" +
            "<select name='"+inputName+"'>" +
            options +
            "</select>" +
            "<input type='hidden' name='"+inputName+"Id' value='"+id+"'>" +
            "<input type='submit' value='+'></form>");
    }
}