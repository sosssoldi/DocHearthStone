function init() {
    displayText('KaraText');
    displayText('LoeText');
    displayText('BrmText');
    displayText('NaxxText');
}
function displayText(id) {
    var colors = {Kara:"#f5b1fa", Loe:"#2cad49", Brm:"#D45A30", Naxx:"#b562e6"};
    var colorsToRead = {Kara:"#f4ccfa", Loe:"#95dea5", Brm:"#f9c8b7", Naxx:"#ead3f7"};
    var p = document.getElementById(id);
    var divID = id.replace("Text","");
    console.log(divID);
    var div = document.getElementById(divID);
    if(p.style.display == 'none') {
        p.style.display = 'block';
        div.style.background = colorsToRead[divID];
    } else {
        p.style.display = 'none';
        div.style.background = colors[divID];
    }
}
