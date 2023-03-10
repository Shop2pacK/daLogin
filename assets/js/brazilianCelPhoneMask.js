function brazilianCelPhoneMask(event) {
    var val = document.getElementById("phone").attributes[0].ownerElement['value'];
    var ret = val.replace(/\D/g, "");
    ret = ret.replace(/^0/, "");
    if (ret.length > 10) {
        ret = ret.replace(/^(\d\d)(\d{5})(\d{4}).*/, "($1) $2-$3");
    } else if (ret.length > 5) {
        if (ret.length == 6 && event.code == "Backspace") { 
            return;
        }
        ret = ret.replace(/^(\d\d)(\d{4})(\d{0,4}).*/, "($1) $2-$3");
    } else if (ret.length > 2) {
        ret = ret.replace(/^(\d\d)(\d{0,5})/, "($1) $2");
    } else {
        if (ret.length != 0) {
            ret = ret.replace(/^(\d*)/, "($1");
        }
    }
    document.getElementById("phone").attributes[0].ownerElement['value'] = ret;
}