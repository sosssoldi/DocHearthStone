function showImg(elem, id) {
	var url = "\"http://media.services.zam.com/v1/media/byName/hs/cards/enus/"+id+".png\"";
	var text = elem.textContent;
	elem.innerHTML = text + "<img src="+url+" style=\"z-index: 1;position: absolute;\"></img>";
}

function hideImg(elem, id) {
	elem.innerHTML = elem.textContent;
}
