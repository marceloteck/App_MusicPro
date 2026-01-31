const tons = ["C", "C#", "D", "D#", "E", "F", "F#", "G", "G#", "A", "A#", "B"];

export default {
  transpor(acorde, passos) {
    let base = acorde.replace(/m|7|sus|dim|aug|\d+/g, "");
    let sufixo = acorde.replace(base, "");
    let index = tons.indexOf(base);
    if (index === -1) return acorde;
    
    let novoIndex = (index + passos + tons.length) % tons.length;
    return tons[novoIndex] + sufixo;
  },
  diferencaTom(origem, destino) {
    let origemIndex = tons.indexOf(origem);
    let destinoIndex = tons.indexOf(destino);
    return destinoIndex - origemIndex;
  }
};
//@resources/plugins/