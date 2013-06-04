Ext.namespace("c2corg.coords");

//// decimal degrees <-> deg/min/sec conversion tools

c2corg.coords.update_decimal = function (field)
{
    var sign;
    var deg = parseInt($(field + '_deg').value, 10);
    if (isNaN(deg))
    {
        deg = 0;
    }
    if (deg < 0)
    {
        sign = -1;
        deg = -1 * deg;
    }
    else
    {
        sign = 1;
    }
    var min = parseFloat($(field + '_min').value);
    if (isNaN(min))
    {
        min = 0;
    }
    var sec = parseFloat($(field + '_sec').value);
    if (isNaN(sec))
    {
        sec = 0;
    }
    $(field).value = sign * Math.round(1000000 * (deg + min/60 + sec/3600)) / 1000000;
};

c2corg.coords.update_degminsec = function (field)
{
    // deal with commas instead of points
    $(field).value = ($(field).value).replace(',', '.');

    if ($(field).value == '') {
        $(field + '_deg').value = $(field + '_min').value = $(field + '_sec').value = '';
        return;
    }

    var sign;
    var degreesTemp = parseFloat($(field).value);
    if (isNaN(degreesTemp))
    {
        return;
    }
    if (degreesTemp < 0)
    {
        sign = -1;
        degreesTemp = -1 * degreesTemp;
    }
    else
    {
        sign = 1;
    }
    var degrees = Math.floor(degreesTemp);

    var minutesTemp = degreesTemp - degrees;
    minutesTemp = 60.0 * minutesTemp;
    var minutes = Math.floor(minutesTemp);

    var secondsTemp = minutesTemp - minutes;
    secondsTemp = 60.0 * secondsTemp;
    var seconds = Math.round(100 * secondsTemp) / 100;

    $(field + '_deg').value = sign * degrees;
    $(field + '_min').value = minutes;
    $(field + '_sec').value = seconds;
};
