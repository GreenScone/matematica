$(document).ready(function(){

setInterval(function(){
        var sec = parseInt($("#seconds").text())+1;
        if(sec<60){
            $("#seconds").text(sec);
        }
        else{
            var min = parseInt($("#minutes").text())+1;
            if(min<60){
                $('#minutes').text(min);
            }
            else{
                var hours = parseInt($('#hours').text())+1;
                if(hours<24){
                    $('#hours').text(hours);
                }
                else{
                    var days = parseInt($('#days').text())+1;
                    if(days<datesInMonth){
                        $('#days').text(days);
                    }
                    else{
                        var months = parseInt($('#months').text())+1;
                        if(months<12){
                            $('#months').text(months);
                        }
                        else{
                            $('#years').text(parseInt($('#years').text())+1);
                            $('#months').text(0);
                        }
                        $('#days').text(0);
                    }

                    $('#hours').text(0);
                }

                $('#minutes').text(0);
            }

            $("#seconds").text(0);
        }
    }

    ,1000);
})

