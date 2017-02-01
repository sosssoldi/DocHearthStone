function check(){
    //valori submittati
    var nome = document.forms["reg"]["nome"].value;
    var cog = document.forms["reg"]["cognome"].value;
    var user = document.forms["reg"]["user"].value;
    var mail = document.forms["reg"]["email"].value;
    var pw = document.forms["reg"]["password"].value;
    var pw2 = document.forms["reg"]["password2"].value;

    //var booleana
    var test = 0;

    //vettore con valori submittati
    var field = [nome, cog, user, mail, pw, pw2];

    //for che cicla per tutti gli input
    for(i = 0; i < field.length; i++){
        if (field[i] == "") {
            test=1;
            document.getElementsByTagName("INPUT")[i].style.borderColor = "red";
            document.getElementsByTagName("INPUT")[i].placeholder = "Compila questo campo!";
        }
    }
    if(test == 1) {
        return false;
    }
    else {
        return true;
    }
}
