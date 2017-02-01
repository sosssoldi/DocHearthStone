function check(){
    //valori submittati
    var nome = document.forms["reg"]["user"].value;
    var pw = document.forms["reg"]["password"].value;

    //var booleana per i campi
    var test = 0;
    //vettore con valori submittati
    var field = [nome, pw];

    //for che cicla per tutti gli input
    for(i = 0; i < field.length; i++){
        if (field[i] == "") {
            test = 1;
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
