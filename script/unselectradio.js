function unselectradio() {
    var myRadios = document.getElementsByClassName('radio');
    var setCheck;
    var x = 0;
    for(x = 0; x < myRadios.length; x++){
            myRadios[x].onclick = function(){
                if(setCheck != this){
                     setCheck = this;
                }else{
                    this.checked = false;
                    setCheck = null;
                }
            };
    }
}
