function atualizarMostrador() {

    const agora = new Date();

    const horas = agora.getHours();
    const minutos = agora.getMinutes();
    const segundos = agora.getSeconds();

    const anguloHora = (horas % 12) * 30 + minutos * 0.5;

    const anguloMinuto = minutos * 6 + segundos * 0.1;

    const ponteiroHora = document.querySelector('.ponteiro-hora');

    const ponteiroMinuto = document.querySelector('.ponteiro-minuto');

    if (ponteiroHora && ponteiroMinuto) {

        ponteiroHora.style.transform =
            `rotate(${anguloHora}deg)`;

        ponteiroMinuto.style.transform =
            `rotate(${anguloMinuto}deg)`;

    }
}

atualizarMostrador();

setInterval(atualizarMostrador, 1000);