var justificationClicked = 0;
var editClicked = 0;

$(document).ready(function(){


    $(document).keyup(function(e) {
        if ((e.keyCode === 27) && (justificationClicked===1)){
            $('#justification').remove();
            justificationClicked = 0;
        }
    });

    $('#showAllWords').click(function () {
        var href = $(location).attr('href');
        window.location.href = href+"&show";
    });


    ///////////ENG

    $("#startRepeatEng").click(function () {
        engSettingsChange(0);
    });

    $("#resetWordsEng").click(function () {
        engSettingsChange(1);
    });

    $("#settingsEng").click(function () {
        engSettingsChange(2);
    });

    $("#changeThisUnitEng").click(function () {
        sendPost('index.php?eng', 'changeThisUnitEng', '1', 'changeThisUnitEng');
    });

    $("#deleteThisUnit").click(function () {
        sendPost('index.php?eng', 'deleteThisUnit', '1', 'deleteThisUnit');
    });

    $("#changeWorkingEng").click(function () {
        sendPost('index.php?eng', 'changeWorkingEng', '1', 'changeWorkingEng');
    });

    $("#changeModeEng").click(function () {
        sendPost('index.php?eng', 'changeModeEng', '1', 'changeModeEng');
    });

    $("#resetAllWordsEng").click(function () {
        sendPost('index.php?eng', 'resetAllWordsEng', '1', 'resetAllWordsEng');
    });

    $("#breakRepeatWordsEng").click(function () {
        sendPost('index.php?eng', 'breakRepeatWordsEng', '1', 'breakRepeatWordsEng');
    });

    $("#startTodayEng").click(function () {
        sendPost('index.php?eng', 'startTodayEng', '1', 'startTodayEng');
    });

    $("#resetTodayWordsEng").click(function () {
        sendPost('index.php?eng', 'resetTodayWordsEng', '1', 'resetTodayWordsEng');
    });


    ///////////DEU

    $("#startRepeatDeu").click(function () {
        engSettingsChange(0);
    });

    $("#resetWordsDeu").click(function () {
        engSettingsChange(1);
    });

    $("#settingsDeu").click(function () {
        engSettingsChange(2);
    });

    $("#changeThisUnitDeu").click(function () {
        sendPost('index.php?deu', 'changeThisUnitDeu', '1', 'changeThisUnitDeu');
    });

    $("#deleteThisUnitDeu").click(function () {
        sendPost('index.php?deu', 'deleteThisUnit', '1', 'deleteThisUnit');
    });

    $("#changeWorkingDeu").click(function () {
        sendPost('index.php?deu', 'changeWorkingDeu', '1', 'changeWorkingDeu');
    });

    $("#changeModeDeu").click(function () {
        sendPost('index.php?deu', 'changeModeDeu', '1', 'changeModeDeu');
    });

    $("#resetAllWordsDeu").click(function () {
        sendPost('index.php?deu', 'resetAllWordsDeu', '1', 'resetAllWordsDeu');
    });

    $("#breakRepeatWordsDeu").click(function () {
        sendPost('index.php?deu', 'breakRepeatWordsDeu', '1', 'breakRepeatWordsDeu');
    });

    $("#startTodayDeu").click(function () {
        sendPost('index.php?deu', 'startTodayDeu', '1', 'startTodayDeu');
    });

    $("#resetTodayWordsDeu").click(function () {
        sendPost('index.php?deu', 'resetTodayWordsDeu', '1', 'resetTodayWordsDeu');
    });


    ///////////JAVA

    $("#startRepeatJava").click(function () {
        engSettingsChange(0);
    });

    $("#resetWordsJava").click(function () {
        engSettingsChange(1);
    });

    $("#settingsJava").click(function () {
        engSettingsChange(2);
    });

    $("#changeThisUnitJava").click(function () {
        sendPost('index.php?java', 'changeThisUnitJava', '1', 'changeThisUnitJava');
    });

    $("#deleteThisUnitJava").click(function () {
        sendPost('index.php?java', 'deleteThisUnit', '1', 'deleteThisUnit');
    });

    $("#changeWorkingJava").click(function () {
        sendPost('index.php?java', 'changeWorkingJava', '1', 'changeWorkingJava');
    });

    $("#changeModeJava").click(function () {
        sendPost('index.php?java', 'changeModeJava', '1', 'changeModeJava');
    });

    $("#resetAllWordsJava").click(function () {
        sendPost('index.php?java', 'resetAllWordsJava', '1', 'resetAllWordsJava');
    });

    $("#breakRepeatWordsJava").click(function () {
        sendPost('index.php?java', 'breakRepeatWordsJava', '1', 'breakRepeatWordsJava');
    });

    $("#startTodayJava").click(function () {
        sendPost('index.php?java', 'startTodayJava', '1', 'startTodayJava');
    });

    $("#resetTodayWordsJava").click(function () {
        sendPost('index.php?java', 'resetTodayWordsJava', '1', 'resetTodayWordsJava');
    });

});

function showJustification(noteId){

    var currentMousePos = { x: -1, y: -1 };
        $('body').click(function (e) {
            if(justificationClicked == 0) {
                if(noteId != 0) {
                    currentMousePos.x = e.pageX;
                    currentMousePos.y = e.pageY - 6;
                    $("body").prepend("<div id='justification' style='position: absolute; left:" + currentMousePos.x + "px;top:" + currentMousePos.y + "px;'><form action='index.php?note' method='POST'><input type='text' id='justificationText' name='justificationText' maxlength='100' /><input type='hidden' name='justificationId' value='"+noteId+"' /></form></div>");
                    $("#justificationText").focus();
                    justificationClicked = 1;
                }
            }
            else{
                $('#justification').remove();
                justificationClicked = 0;
            }
        });

}

function editFirstWord(first, id){
    var href = $(location).attr('href');
    href = href.replace('&show','');
    if(editClicked === 0) {
        editClicked = 1;
        var text = $(first).text();
        text=text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g,'&apos;');
        $(first).html("<form action='"+href+"' method='post'><input name='editValueFirstWord' type='text' maxlength='1000' size='5' value='" + text + "'><input type='hidden' name='editFirstWord' value='"+id+"'></form>");
    }
}

function editSecondWord(second, id){
    var href = $(location).attr('href');
    href = href.replace('&show','');
    if(editClicked === 0) {
        editClicked = 1;
        var text = $(second).text();
        text=text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g,'&apos;');
        $(second).html("<form action='"+href+"' method='post'><input name='editValueSecondWord' type='text' maxlength='1000' size='5' value='" + text + "'><input type='hidden' name='editSecondWord' value='"+id+"'></form>");
    }
}

function editFirstWordMoreCharacters(first, id){
    var href = $(location).attr('href');
    href = href.replace('&show','');
    if(editClicked === 0) {
        editClicked = 1;
        var text = $(first).text();
        text=text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g,'&apos;');
        $(first).html("<form action='"+href+"' method='post'><input name='editValueFirstWord' type='text' maxlength='3000' size='5' value='" + text + "'><input type='hidden' name='editFirstWord' value='"+id+"'></form>");
    }
}

function editSecondWordMoreCharacters(second, id){
    var href = $(location).attr('href');
    href = href.replace('&show','');
    if(editClicked === 0) {
        editClicked = 1;
        var text = $(second).text();
        text=text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g,'&apos;');
        $(second).html("<form action='"+href+"' method='post'><input name='editValueSecondWord' type='text' maxlength='3000' size='5' value='" + text + "'><input type='hidden' name='editSecondWord' value='"+id+"'></form>");
    }
}


function engSettingsChange(i){
    for(var j=0; j<$(".engSettings").length; j++){
        $(".engSettings").eq(j).css('display','none');
    }

    $(".engSettings").eq(i).css('display','block');

}

function sendPost(adress, inputName, inputValue, idElementDiv){
    var theForm, newInput1;

    theForm = document.createElement('form');
    theForm.action = adress;
    theForm.method = 'post';
    theForm.style = 'display: none;';
    newInput1 = document.createElement('input');
    newInput1.type = 'hidden';
    newInput1.name = inputName;
    newInput1.value = inputValue;
    newInput1.style = 'display: none;';
    theForm.appendChild(newInput1);
    document.getElementById(idElementDiv).appendChild(theForm);
    theForm.submit();
}
