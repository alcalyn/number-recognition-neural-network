$(function () {
    var websocketSession = false;
    var myBoard = new DrawingBoard.Board('zbeubeu', {
        controls: [
            {
                Navigation: true
            }
        ],
        size: 32
    });

    //myBoard.ev.bind('board:startDrawing', console.log);
    //myBoard.ev.bind('board:drawing', recognize);
    //myBoard.ev.bind('board:stopDrawing', console.log);

    myBoard.ev.bind('board:stopDrawing', recognize);

    function recognize() {
        if (!websocketSession) {
            console.warn('Not yet connected to websocket server.')
            return;
        }

        var resizedCanvas = document.createElement('canvas');
        var resizedContext = resizedCanvas.getContext('2d');

        resizedCanvas.width = '14';
        resizedCanvas.height = '14';

        resizedContext.drawImage(myBoard.canvas, 0, 0, 14, 14);
        var img = resizedCanvas.toDataURL();

        $('#zbeubeu-thumb')
            .empty()
            .append('<img src="'+resizedCanvas.toDataURL()+'" width="56" height="56" />')
        ;

        websocketSession.publish('network', img);
    }

    ab.connect('ws://0.0.0.0:8582', onSessionOpen, console.warn);

    function onSessionOpen(session) {
        websocketSession = session;

        session.subscribe('network', function (topic, event) {
            console.log(event);

            if ('recognize' === event.type) {
                var percents = [];
                for (var i = 0; i < 10; i++) {
                    if (event.recognize[i] > 0.1) {
                        percents.push({
                            number: i,
                            percent: event.recognize[i]
                        });
                    }
                }

                percents.sort(function (a, b) {
                    return a.percent < b.percent ? 1 : -1;
                });

                $('#result').empty();

                if (0 === percents.length) {
                    $('#result').append('<span style="font-size:42px">?</span>');
                }

                for (var i = 0; i < percents.length; i++) {
                    var p = percents[i];
                    $('#result').append('<span style="font-size:'+(p.percent * 64)+'px">'+p.number+'</span>');
                }
            }
        });
    }
});
