function showImage(x) {
	//AJAX per ottenere il link della carta
	var carta = document.getElementsByTagName("span")[0].firstChild.nodeValue;
	var indirizzo = "../images/" + carta + ".png";
	var descrizione = document.getElementsByTagName("span")[1].firstChild.nodeValue;

	x.innerHTML = "<span>" + carta + "</span><span class=\"descrizione\">" + descrizione + "</span><img src = \"" + indirizzo + "\">";
}

function hideImage(x) {
	var carta = document.getElementsByTagName("span")[0].firstChild.nodeValue;
	var descrizione = document.getElementsByTagName("span")[1].firstChild.nodeValue;
	
	x.innerHTML = "<span>" + carta + "</span><span class=\"descrizione\">" + descrizione + "</span>";
}