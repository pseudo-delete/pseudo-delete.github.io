<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gero - Main Menu</title>

    <link rel="stylesheet" href="index.css">

    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="ajax.js"></script><!-- all about php and ajax, with database functions-->

    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>

    <!-- <script type="module" src="index.js"></script>all about firebase functions -->
</head>
<body>
    <a href="../"><h1> - GERO AI- </h1></a>
    <div class="timer-container">
        <label id="timer">0</label>
        <button id="btn-start-timer">Start</button>
        <button id="btn-stop-timer">Stop</button>
        <button id="btn-reset-timer">Reset</button>
        <!-- timer functions script scroll down because it uses function that needs to go first -->

        <div class="current-pixel-id-display-container">
            <label>Current Pixel ID: <span id="current-pixel-id-display">0/0</span></label>
            <label>| Current Cycle: <span id="cycle-counter">0</span></label>
            <label>X:<span id="x-id">0</span>px Y:<span id="y-id">0</span>px</label>
        </div>
    </div>

    <div class="main">
        <div class="div-neural-net-value">
            <h5>Weight, Biases, and Target values</h5>
            <p>
                Doc ID: <span id="doc-id-val">0</span><br>
                ID: <span id="id-val">0</span><br><br>

                W1: <span id="weight1-val">0</span><br>
                W2: <span id="weight2-val">0</span><br>
                W3: <span id="weight3-val">0</span><br>
                W4: <span id="weight4-val">0</span><br>
                W5: <span id="weight5-val">0</span><br>
                W6: <span id="weight6-val">0</span><br>
                W7: <span id="weight7-val">0</span><br>
                W8: <span id="weight8-val">0</span><br>
                W9: <span id="weight9-val">0</span><br>
                W10: <span id="weight10-val">0</span><br>
                W11: <span id="weight11-val">0</span><br>
                W12: <span id="weight12-val">0</span><br>
                W13: <span id="weight13-val">0</span><br>
                W14: <span id="weight14-val">0</span><br>
                W15: <span id="weight15-val">0</span><br>
                W16: <span id="weight16-val">0</span><br>
                W17: <span id="weight17-val">0</span><br>
                W18: <span id="weight18-val">0</span><br><br>

                B1(For H1): <span id="bias1-val">0</span><br>
                B2(For O1): <span id="bias2-val">0</span><br>
                B3(For H2): <span id="bias3-val">0</span><br>
                B4(For O2): <span id="bias4-val">0</span><br>
                B5(For H3): <span id="bias5-val">0</span><br>
                B6(For O3): <span id="bias6-val">0</span><br><br>

                T1: <span id="target1-val">0</span><br>
                T2: <span id="target2-val">0</span><br>
                T3: <span id="target3-val">0</span><br><br>
            </p>
        </div>
        
        <div class = "panel-ai">
            <!-- Neural Network Div-->
            <div class="div-neural-net">
                <h4>Neural Network</h4>
                <button id="btn-generate-neural-val" type="button">Re/generate Neural Values</button>
                <button id="btn-clear-neural-val" type="button">Clear Neural Values</button>
                <div class="neural-zone">
                    <div class="neural-row">
                        <div class="circle-input"><label class="neuron-value" id="input1-val">0</label></div>
                        <div class="line-container">
                            <!--<div>
                                <label class="weight-value" id="weight1-val">0</label>
                                <label class="bias-value" id="bias1-val">0</label>
                            </div><br>-->
                            <canvas id="line1-canvas" width="100" height="100">
                                
                            </canvas>
                            <script>
                                $(function()
                                {
                                    let lineCanvas1 = $("#line1-canvas")[0];
                                    let canvasContext1 = lineCanvas1.getContext("2d");

                                    // drawing line from point a to point b - weight 1
                                    canvasContext1.beginPath();
                                    canvasContext1.moveTo(0, 50);
                                    canvasContext1.lineTo(100, 50);
                                    canvasContext1.strokeStyle = "black";
                                    canvasContext1.lineWidth = 1;
                                    canvasContext1.stroke();

                                    // drawing line from point a to point b - weight 3
                                    canvasContext1.beginPath();
                                    canvasContext1.moveTo(50, 100);
                                    canvasContext1.lineTo(100, 50);
                                    canvasContext1.strokeStyle = "black";
                                    canvasContext1.lineWidth = 1;
                                    canvasContext1.stroke();

                                    // drawing line from point a to point b - weight 4
                                    canvasContext1.beginPath();
                                    canvasContext1.moveTo(0, 50);
                                    canvasContext1.lineTo(50, 100);
                                    canvasContext1.strokeStyle = "black";
                                    canvasContext1.lineWidth = 1;
                                    canvasContext1.stroke();

                                    // drawing line from point a to point b - weight 9
                                    canvasContext1.beginPath();
                                    canvasContext1.moveTo(75, 100);
                                    canvasContext1.lineTo(100, 50);
                                    canvasContext1.strokeStyle = "black";
                                    canvasContext1.lineWidth = 1;
                                    canvasContext1.stroke();

                                    // drawing line from point a to point b - weight 11
                                    canvasContext1.beginPath();
                                    canvasContext1.moveTo(0, 50);
                                    canvasContext1.lineTo(25, 100);
                                    canvasContext1.strokeStyle = "black";
                                    canvasContext1.lineWidth = 1;
                                    canvasContext1.stroke();

                                    /* drawing text labeling the lines */
                                    // set font style and size
                                    canvasContext1.font = "10px Arial";
                                    // set text color
                                    canvasContext1.fillStyle = "white";
                                    // draw filled text
                                    canvasContext1.fillText("W1", 43, 53);

                                    // set font style and size
                                    canvasContext1.font = "10px Arial";
                                    // set text color
                                    canvasContext1.fillStyle = "white";
                                    // draw filled text
                                    canvasContext1.fillText("W4", 18, 73);

                                    // set font style and size
                                    canvasContext1.font = "10px Arial";
                                    // set text color
                                    canvasContext1.fillStyle = "white";
                                    // draw filled text
                                    canvasContext1.fillText("W11", 3, 93);
                                });
                            </script>
                            <!--<div class="line-horizontal"></div>-->
                        </div><!--input1-hidden1 weight-->
                        <div class="circle-hidden"><label class="neuron-value" id="hidden1-val">0</label></div>
                        <div class="line-container">
                            <!--<div>
                                <label class="weight-value" id="weight2-val">0</label>
                                <label class="bias-value" id="bias2-val">0</label>
                            </div>-->
                            <canvas id="line2-canvas" width="100" height="100"></canvas>
                            <script>
                                $(function()
                                {
                                    let lineCanvas2 = $("#line2-canvas")[0];
                                    let canvasContext2 = lineCanvas2.getContext("2d");

                                    // drawing line from point a to point b
                                    canvasContext2.beginPath();
                                    canvasContext2.moveTo(0, 50);
                                    canvasContext2.lineTo(100, 50);
                                    canvasContext2.strokeStyle = "black";
                                    canvasContext2.lineWidth = 1;
                                    canvasContext2.stroke();

                                    // drawing line from point a to point b
                                    canvasContext2.beginPath();
                                    canvasContext2.moveTo(50, 100);
                                    canvasContext2.lineTo(100, 50);
                                    canvasContext2.strokeStyle = "black";
                                    canvasContext2.lineWidth = 1;
                                    canvasContext2.stroke();

                                    // drawing line from point a to point b
                                    canvasContext2.beginPath();
                                    canvasContext2.moveTo(0, 50);
                                    canvasContext2.lineTo(50, 100);
                                    canvasContext2.strokeStyle = "black";
                                    canvasContext2.lineWidth = 1;
                                    canvasContext2.stroke();

                                    // drawing line from point a to point b - weight 14
                                    canvasContext2.beginPath();
                                    canvasContext2.moveTo(75, 100);
                                    canvasContext2.lineTo(100, 50);
                                    canvasContext2.strokeStyle = "black";
                                    canvasContext2.lineWidth = 1;
                                    canvasContext2.stroke();

                                    // drawing line from point a to point b - weight 16
                                    canvasContext2.beginPath();
                                    canvasContext2.moveTo(0, 50);
                                    canvasContext2.lineTo(25, 100);
                                    canvasContext2.strokeStyle = "black";
                                    canvasContext2.lineWidth = 1;
                                    canvasContext2.stroke();

                                    /* drawing text labeling the lines */
                                    // set font style and size
                                    canvasContext2.font = "10px Arial";
                                    // set text color
                                    canvasContext2.fillStyle = "white";
                                    // draw filled text
                                    canvasContext2.fillText("W2", 43, 53);

                                    // set font style and size
                                    canvasContext2.font = "10px Arial";
                                    // set text color
                                    canvasContext2.fillStyle = "white";
                                    // draw filled text
                                    canvasContext2.fillText("W7", 23, 73);

                                    // set font style and size
                                    canvasContext2.font = "10px Arial";
                                    // set text color
                                    canvasContext2.fillStyle = "white";
                                    // draw filled text
                                    canvasContext2.fillText("W16", 23, 73);
                                });
                            </script>
                            <!--<div class="line-horizontal"></div>-->
                        </div><!--hidden1-output1 weight-->
                        <div class="circle-output"><label class="neuron-value" id="output1-val">0</label></div><br>
                    </div>
                    
                    <div class="neural-row">
                        <div class="circle-input"><label class="neuron-value" id="input2-val">0</label></div>
                        <div class="line-container">
                            <!--<div>
                                <label class="weight-value" id="weight3-val">0</label>
                                <label class="bias-value" id="bias1-val">0</label>
                            </div>-->
                            <canvas id="line3-canvas" width="100" height="100"></canvas>
                            <script>
                                $(function()
                                {
                                    let lineCanvas3 = $("#line3-canvas")[0];
                                    let canvasContext3 = lineCanvas3.getContext("2d");

                                    // drawing line from point a to point b
                                    canvasContext3.beginPath();
                                    canvasContext3.moveTo(0, 50);
                                    canvasContext3.lineTo(100, -50);
                                    canvasContext3.strokeStyle = "black";
                                    canvasContext3.lineWidth = 1;
                                    canvasContext3.stroke();
                                    
                                    // drawing line from point a to point b - weight 4
                                    canvasContext3.beginPath();
                                    canvasContext3.moveTo(50, 0);
                                    canvasContext3.lineTo(100, 50);
                                    canvasContext3.strokeStyle = "black";
                                    canvasContext3.lineWidth = 1;
                                    canvasContext3.stroke();

                                    // drawing line from point a to point b - weight 5
                                    canvasContext3.beginPath();
                                    canvasContext3.moveTo(0, 50);
                                    canvasContext3.lineTo(100, 50);
                                    canvasContext3.strokeStyle = "black";
                                    canvasContext3.lineWidth = 1;
                                    canvasContext3.stroke();

                                    // drawing line from point a to point b - weight 9
                                    canvasContext3.beginPath();
                                    canvasContext3.moveTo(25, 100);
                                    canvasContext3.lineTo(75, 0);
                                    canvasContext3.strokeStyle = "black";
                                    canvasContext3.lineWidth = 1;
                                    canvasContext3.stroke();

                                    // drawing line from point a to point b - weight 10
                                    canvasContext3.beginPath();
                                    canvasContext3.moveTo(50, 100);
                                    canvasContext3.lineTo(100, 50);
                                    canvasContext3.strokeStyle = "black";
                                    canvasContext3.lineWidth = 1;
                                    canvasContext3.stroke();

                                    // drawing line from point a to point b - weight 11
                                    canvasContext3.beginPath();
                                    canvasContext3.moveTo(25, 0);
                                    canvasContext3.lineTo(75, 100);
                                    canvasContext3.strokeStyle = "black";
                                    canvasContext3.lineWidth = 1;
                                    canvasContext3.stroke();

                                    // drawing line from point a to point b - weight 12
                                    canvasContext3.beginPath();
                                    canvasContext3.moveTo(0, 50);
                                    canvasContext3.lineTo(50, 100);
                                    canvasContext3.strokeStyle = "black";
                                    canvasContext3.lineWidth = 1;
                                    canvasContext3.stroke();

                                    /* drawing text labeling the lines */
                                    // set font style and size
                                    canvasContext3.font = "10px Arial";
                                    // set text color
                                    canvasContext3.fillStyle = "white";
                                    // draw filled text
                                    canvasContext3.fillText("W3", 23, 23);
                                    
                                    /* drawing text labeling the lines */
                                    // set font style and size
                                    canvasContext3.font = "10px Arial";
                                    // set text color
                                    canvasContext3.fillStyle = "white";
                                    // draw filled text
                                    canvasContext3.fillText("W5", 23, 53);

                                    /* drawing text labeling the lines */
                                    // set font style and size
                                    canvasContext3.font = "10px Arial";
                                    // set text color
                                    canvasContext3.fillStyle = "white";
                                    // draw filled text
                                    canvasContext3.fillText("W12", 13, 73);
                                });
                            </script>
                            <!--<div class="line-horizontal"></div>-->
                        </div><!--input2-hidden2 weight-->

                        <div class="circle-hidden"><label class="neuron-value" id="hidden2-val">0</label></div>
                        <div class="line-container">
                            <!--<div>
                                <label class="weight-value" id="weight4-val">0</label>
                                <label class="bias-value" id="bias3-val">0</label>
                            </div>-->
                            <canvas id="line4-canvas" width="100" height="100"></canvas>
                            <script>
                                $(function()
                                {
                                    let lineCanvas4 = $("#line4-canvas")[0];
                                    let canvasContext4 = lineCanvas4.getContext("2d");

                                    // drawing line from point a to point b
                                    canvasContext4.beginPath();
                                    canvasContext4.moveTo(0, 50);
                                    canvasContext4.lineTo(50, 0);
                                    canvasContext4.strokeStyle = "black";
                                    canvasContext4.lineWidth = 1;
                                    canvasContext4.stroke();

                                    // drawing line from point a to point b
                                    canvasContext4.beginPath();
                                    canvasContext4.moveTo(0, 50);
                                    canvasContext4.lineTo(100, 50);
                                    canvasContext4.strokeStyle = "black";
                                    canvasContext4.lineWidth = 1;
                                    canvasContext4.stroke();

                                    // drawing line from point a to point b
                                    canvasContext4.beginPath();
                                    canvasContext4.moveTo(50, 0);
                                    canvasContext4.lineTo(100, 50);
                                    canvasContext4.strokeStyle = "black";
                                    canvasContext4.lineWidth = 1;
                                    canvasContext4.stroke();

                                    // drawing line from point a to point b - weight 14
                                    canvasContext4.beginPath();
                                    canvasContext4.moveTo(25, 100);
                                    canvasContext4.lineTo(75, 0);
                                    canvasContext4.strokeStyle = "black";
                                    canvasContext4.lineWidth = 1;
                                    canvasContext4.stroke();

                                    // drawing line from point a to point b - weight 15
                                    canvasContext4.beginPath();
                                    canvasContext4.moveTo(50, 100);
                                    canvasContext4.lineTo(100, 50);
                                    canvasContext4.strokeStyle = "black";
                                    canvasContext4.lineWidth = 1;
                                    canvasContext4.stroke();

                                    // drawing line from point a to point b - weight 16
                                    canvasContext4.beginPath();
                                    canvasContext4.moveTo(25, 0);
                                    canvasContext4.lineTo(75, 100);
                                    canvasContext4.strokeStyle = "black";
                                    canvasContext4.lineWidth = 1;
                                    canvasContext4.stroke();

                                    // drawing line from point a to point b - weight 17
                                    canvasContext4.beginPath();
                                    canvasContext4.moveTo(0, 50);
                                    canvasContext4.lineTo(50, 100);
                                    canvasContext4.strokeStyle = "black";
                                    canvasContext4.lineWidth = 1;
                                    canvasContext4.stroke();

                                    /* drawing text labeling the lines */
                                    // set font style and size
                                    canvasContext4.font = "10px Arial";
                                    // set text color
                                    canvasContext4.fillStyle = "white";
                                    // draw filled text
                                    canvasContext4.fillText("W6", 23, 23);

                                    // set font style and size
                                    canvasContext4.font = "10px Arial";
                                    // set text color
                                    canvasContext4.fillStyle = "white";
                                    // draw filled text
                                    canvasContext4.fillText("W8", 43, 53);

                                    // set font style and size
                                    canvasContext4.font = "10px Arial";
                                    // set text color
                                    canvasContext4.fillStyle = "white";
                                    // draw filled text
                                    canvasContext4.fillText("W17", 23, 73);
                                });
                            </script>
                            <!--<div class="line-horizontal"></div>-->
                        </div>
                        <div class="circle-output"><label class="neuron-value" id="output2-val">0</label></div><br>
                    </div>

                    <div class="neural-row">
                        <div class="circle-input"><label class="neuron-value" id="input3-val">0</label></div>
                        <div class="line-container">
                            <canvas id="line5-canvas" width="100" height="100"></canvas>
                            <script>
                                $(function()
                                {
                                    let lineCanvas5 = $("#line5-canvas")[0];
                                    let canvasContext5 = lineCanvas5.getContext("2d");

                                    // drawing line from point a to point b - weight 9
                                    canvasContext5.beginPath();
                                    canvasContext5.moveTo(0, 50);
                                    canvasContext5.lineTo(25, 0);
                                    canvasContext5.strokeStyle = "black";
                                    canvasContext5.lineWidth = 1;
                                    canvasContext5.stroke();
                                    
                                    // drawing line from point a to point b - weight 10
                                    canvasContext5.beginPath();
                                    canvasContext5.moveTo(0, 50);
                                    canvasContext5.lineTo(50, 0);
                                    canvasContext5.strokeStyle = "black";
                                    canvasContext5.lineWidth = 1;
                                    canvasContext5.stroke();

                                    // drawing line from point a to point b - weight 11
                                    canvasContext5.beginPath();
                                    canvasContext5.moveTo(75, 0);
                                    canvasContext5.lineTo(100, 50);
                                    canvasContext5.strokeStyle = "black";
                                    canvasContext5.lineWidth = 1;
                                    canvasContext5.stroke();

                                    // drawing line from point a to point b - weight 12
                                    canvasContext5.beginPath();
                                    canvasContext5.moveTo(50, 0);
                                    canvasContext5.lineTo(100, 50);
                                    canvasContext5.strokeStyle = "black";
                                    canvasContext5.lineWidth = 1;
                                    canvasContext5.stroke();

                                    // drawing line from point a to point b - weight 13
                                    canvasContext5.beginPath();
                                    canvasContext5.moveTo(0, 50);
                                    canvasContext5.lineTo(100, 50);
                                    canvasContext5.strokeStyle = "black";
                                    canvasContext5.lineWidth = 1;
                                    canvasContext5.stroke();

                                    /* drawing text labeling the lines */
                                    // set font style and size
                                    canvasContext5.font = "10px Arial";
                                    // set text color
                                    canvasContext5.fillStyle = "white";
                                    // draw filled text
                                    canvasContext5.fillText("W9", 3, 23);

                                    // set font style and size
                                    canvasContext5.font = "10px Arial";
                                    // set text color
                                    canvasContext5.fillStyle = "white";
                                    // draw filled text
                                    canvasContext5.fillText("W10", 23, 23);

                                    // set font style and size
                                    canvasContext5.font = "10px Arial";
                                    // set text color
                                    canvasContext5.fillStyle = "white";
                                    // draw filled text
                                    canvasContext5.fillText("W13", 23, 53);
                                });
                            </script>
                            <!--<div class="line-horizontal"></div>-->
                        </div>
                        <div class="circle-hidden"><label class="neuron-value" id="hidden3-val">0</label></div>
                        <div class="line-container">
                            <canvas id="line6-canvas" width="100" height="100"></canvas>
                            <script>
                                $(function()
                                {
                                    let lineCanvas6 = $("#line6-canvas")[0];
                                    let canvasContext6 = lineCanvas6.getContext("2d");

                                    // drawing line from point a to point b - weight 14
                                    canvasContext6.beginPath();
                                    canvasContext6.moveTo(0, 50);
                                    canvasContext6.lineTo(25, 0);
                                    canvasContext6.strokeStyle = "black";
                                    canvasContext6.lineWidth = 1;
                                    canvasContext6.stroke();
                                    
                                    // drawing line from point a to point b - weight 15
                                    canvasContext6.beginPath();
                                    canvasContext6.moveTo(0, 50);
                                    canvasContext6.lineTo(50, 0);
                                    canvasContext6.strokeStyle = "black";
                                    canvasContext6.lineWidth = 1;
                                    canvasContext6.stroke();

                                    // drawing line from point a to point b - weight 16
                                    canvasContext6.beginPath();
                                    canvasContext6.moveTo(75, 0);
                                    canvasContext6.lineTo(100, 50);
                                    canvasContext6.strokeStyle = "black";
                                    canvasContext6.lineWidth = 1;
                                    canvasContext6.stroke();

                                    // drawing line from point a to point b - weight 17
                                    canvasContext6.beginPath();
                                    canvasContext6.moveTo(50, 0);
                                    canvasContext6.lineTo(100, 50);
                                    canvasContext6.strokeStyle = "black";
                                    canvasContext6.lineWidth = 1;
                                    canvasContext6.stroke();

                                    // drawing line from point a to point b - weight 18
                                    canvasContext6.beginPath();
                                    canvasContext6.moveTo(0, 50);
                                    canvasContext6.lineTo(100, 50);
                                    canvasContext6.strokeStyle = "black";
                                    canvasContext6.lineWidth = 1;
                                    canvasContext6.stroke();

                                    /* drawing text labeling the lines */
                                    // set font style and size
                                    canvasContext6.font = "10px Arial";
                                    // set text color
                                    canvasContext6.fillStyle = "white";
                                    // draw filled text
                                    canvasContext6.fillText("W14", 3, 23);

                                    // set font style and size
                                    canvasContext6.font = "10px Arial";
                                    // set text color
                                    canvasContext6.fillStyle = "white";
                                    // draw filled text
                                    canvasContext6.fillText("W15", 23, 23);

                                    // set font style and size
                                    canvasContext6.font = "10px Arial";
                                    // set text color
                                    canvasContext6.fillStyle = "white";
                                    // draw filled text
                                    canvasContext6.fillText("W18", 23, 53);
                                });
                            </script>
                            <!--<div class="line-horizontal"></div>-->
                        </div>
                        <div class="circle-output"><label class="neuron-value" id="output3-val">0</label></div>
                    </div>
                    <button id="btn-fire" type="button">Fire</button>
                    <button id="btn-train" type="button">Train</button>
                    
                    <script>
                        $(function()
                        {
                            function sigmoid(x)
                            {
                                return 1/(1+Math.exp(-x));
                            }

                            // transferring the information from data-table to be the current selected value in the network model applying it's values
                            function tableToNetwork(id)
                            {
                                // make it from php table to network
                                //$.ajax();


                                $("#input1-val").text($("#input1-"+id).text());// input1-val
                                $("#input2-val").text($("#input2-"+id).text());// input2-val
                                $("#input3-val").text($("#input3-"+id).text());// input3-val

                                $("#hidden1-val").text($("#hidden1-"+id).text());// hidden1-val
                                $("#hidden2-val").text($("#hidden2-"+id).text());// hidden2-val
                                $("#hidden3-val").text($("#hidden3-"+id).text());// hidden3-val

                                $("#output1-val").text($("#output1-"+id).text());// output1-val
                                $("#output2-val").text($("#output2-"+id).text());// output2-val
                                $("#output3-val").text($("#output3-"+id).text());// output3-val

                                // $("#doc-id-val").text($("#doc-id-"+id).text());// document id value
                                $("#id-val").text($("#id-"+id).text());// field id value

                                $("#weight1-val").text($("#weight1-"+id).text());// weight1-val
                                $("#weight2-val").text($("#weight2-"+id).text());// weight2-val
                                $("#weight3-val").text($("#weight3-"+id).text());// weight3-val
                                $("#weight4-val").text($("#weight4-"+id).text());// weight4-val
                                $("#weight5-val").text($("#weight5-"+id).text());// weight5-val
                                $("#weight6-val").text($("#weight6-"+id).text());// weight6-val
                                $("#weight7-val").text($("#weight7-"+id).text());// weight7-val
                                $("#weight8-val").text($("#weight8-"+id).text());// weight8-val
                                $("#weight9-val").text($("#weight9-"+id).text());// weight9-val
                                $("#weight10-val").text($("#weight10-"+id).text());// weight10-val
                                $("#weight11-val").text($("#weight11-"+id).text());// weight11-val
                                $("#weight12-val").text($("#weight12-"+id).text());// weight12-val
                                $("#weight13-val").text($("#weight13-"+id).text());// weight13-val
                                $("#weight14-val").text($("#weight14-"+id).text());// weight14-val
                                $("#weight15-val").text($("#weight15-"+id).text());// weight15-val
                                $("#weight16-val").text($("#weight16-"+id).text());// weight16-val
                                $("#weight17-val").text($("#weight17-"+id).text());// weight17-val
                                $("#weight18-val").text($("#weight18-"+id).text());// weight18-val

                                $("#bias1-val").text($("#bias1-"+id).text());// bias1-val
                                $("#bias2-val").text($("#bias2-"+id).text());// bias2-val
                                $("#bias3-val").text($("#bias3-"+id).text());// bias3-val
                                $("#bias4-val").text($("#bias4-"+id).text());// bias4-val
                                $("#bias5-val").text($("#bias5-"+id).text());// bias5-val
                                $("#bias6-val").text($("#bias6-"+id).text());// bias6-val

                                $("#target1-val").text($("#target1-"+id).text());// target1-val
                                $("#target2-val").text($("#target2-"+id).text());// target2-val
                                $("#target3-val").text($("#target3-"+id).text());// target3-val
                            }
                            /* end of function tableToNetwork(id) */

                            function networkToTable(id)
                            {
                                // make it from network to php table using ajax
                                //$.ajax();

                                let input = [];
                                input.push($("#input1-val").text());// input1-val
                                input.push($("#input2-val").text());// input2-val
                                input.push($("#input3-val").text());// input3-val

                                let hidden = [];
                                hidden.push($("#hidden1-val").text());// hidden1-val
                                hidden.push($("#hidden2-val").text());// hidden2-val
                                hidden.push($("#hidden3-val").text());// hidden3-val

                                let output = [];
                                output.push($("#output1-val").text());// output1-val
                                output.push($("#output2-val").text());// output2-val
                                output.push($("#output3-val").text());// output3-val

                                $("#doc-id-val").text();// document id value
                                $("#id-val").text();// field id value

                                let weight = [];
                                weight.push($("#weight1-val").text());// weight1-val
                                weight.push($("#weight2-val").text());// weight2-val
                                weight.push($("#weight3-val").text());// weight3-val
                                weight.push($("#weight4-val").text());// weight4-val
                                weight.push($("#weight5-val").text());// weight5-val
                                weight.push($("#weight6-val").text());// weight6-val
                                weight.push($("#weight7-val").text());// weight7-val
                                weight.push($("#weight8-val").text());// weight8-val
                                weight.push($("#weight9-val").text());// weight9-val
                                weight.push($("#weight10-val").text());// weight10-val
                                weight.push($("#weight11-val").text());// weight11-val
                                weight.push($("#weight12-val").text());// weight12-val
                                weight.push($("#weight13-val").text());// weight13-val
                                weight.push($("#weight14-val").text());// weight14-val
                                weight.push($("#weight15-val").text());// weight15-val
                                weight.push($("#weight16-val").text());// weight16-val
                                weight.push($("#weight17-val").text());// weight17-val
                                weight.push($("#weight18-val").text());// weight18-val

                                let bias = [];
                                bias.push($("#bias1-val").text());// bias1-val
                                bias.push($("#bias2-val").text());// bias2-val
                                bias.push($("#bias3-val").text());// bias3-val
                                bias.push($("#bias4-val").text());// bias4-val
                                bias.push($("#bias5-val").text());// bias5-val
                                bias.push($("#bias6-val").text());// bias6-val

                                let target = [];
                                target.push($("#target1-val").text());// target1-val
                                target.push($("#target2-val").text());// target2-val
                                target.push($("#target3-val").text());// target3-val

                                updateNeuralDataRowDb(id, input, hidden, output, target, weight, bias);
                            }

                            
                            const canvasAi = document.getElementById("ai-canvas-display");
                            const ctxAi = canvasAi.getContext("2d");
                            function fire()
                            {
                                let input1 = parseFloat($("#input1-val").text());
                                let input2 = parseFloat($("#input2-val").text());
                                let input3 = parseFloat($("#input3-val").text());
                                
                                let weight1 = parseFloat($("#weight1-val").text());// input1 - hidden1
                                let weight2 = parseFloat($("#weight2-val").text());// hidden1 - output1
                                let weight3 = parseFloat($("#weight3-val").text());// input2 - hidden1
                                let weight4 = parseFloat($("#weight4-val").text());// input1 - hidden2
                                let weight5 = parseFloat($("#weight5-val").text());// input2 - hidden2
                                let weight6 = parseFloat($("#weight6-val").text());// hidden2 - output1
                                let weight7 = parseFloat($("#weight7-val").text());// hidden1 - output2
                                let weight8 = parseFloat($("#weight8-val").text());// hidden2 - output2
                                let weight9 = parseFloat($("#weight9-val").text());// input3 - hidden1
                                let weight10 = parseFloat($("#weight10-val").text());// input3 - hidden2
                                let weight11 = parseFloat($("#weight11-val").text());// input1 - hidden3
                                let weight12 = parseFloat($("#weight12-val").text());// input2 - hidden3
                                let weight13 = parseFloat($("#weight13-val").text());// input3 - hidden3
                                let weight14 = parseFloat($("#weight14-val").text());// hidden3 - output1
                                let weight15 = parseFloat($("#weight15-val").text());// hidden3 - output2
                                
                                let weight16 = parseFloat($("#weight16-val").text());// hidden1 - output3
                                let weight17 = parseFloat($("#weight17-val").text());// hidden2 - output3
                                let weight18 = parseFloat($("#weight18-val").text());// hidden3 - output3

                                let bias1 = parseFloat($("#bias1-val").text());// to hidden1 bias
                                let bias2 = parseFloat($("#bias2-val").text());// to output1 bias
                                let bias3 = parseFloat($("#bias2-val").text());// to hidden2 bias
                                let bias4 = parseFloat($("#bias4-val").text());// to output2 bias
                                let bias5 = parseFloat($("#bias5-val").text());// to hidden3 bias
                                let bias6 = parseFloat($("#bias6-val").text());// to output3 bias
                                
                                let hidden1 = sigmoid((weight1*input1) + (weight3*input2) + (weight9*input3) + bias1);
                                $("#hidden1-val").text(hidden1);

                                let hidden2 = sigmoid((weight4*input1) + (weight5*input2) + (weight10*input3) + bias3);
                                $("#hidden2-val").text(hidden2);
                                
                                let hidden3 = sigmoid((weight11*input1) + (weight12*input2) + (weight13*input3) + bias5);
                                $("#hidden3-val").text(hidden3);
                                
                                let output1 = sigmoid((weight2*hidden1) + (weight6*hidden2) + (weight14*hidden3) + bias2);
                                $("#output1-val").text(output1);

                                let output2 = sigmoid((weight8*hidden1) + (weight7*hidden2) + (weight15*hidden3) + bias4);
                                $("#output2-val").text(output2);

                                let output3 = sigmoid((weight16*hidden1) + (weight17*hidden2) + (weight18*hidden3) + bias6);
                                $("#output3-val").text(output3);

                                // setting ai-pixel1 color based on fired value
                                let r1 = parseInt(parseFloat($("#output1-val").text()) * 256);// Output1: R
                                let g1 = parseInt(parseFloat($("#output2-val").text()) * 256);// Output1: G
                                let b1 = parseInt(parseFloat($("#output3-val").text()) * 256);// Output1: B
                                // pixel1 - (RGB):
                                // $(".ai-pixel1").css("background-color", "rgb("+ r1 + ", " + g1 + ", " + b1 + ")");

                                let xAi = parseInt($("#x-id").text());
                                let yAi = parseInt($("#y-id").text());

                                ctxAi.fillStyle = `rgb(${r1}, ${g1}, ${b1})`;
                                ctxAi.fillRect(xAi, yAi, 1, 1); // 1x1 pixel
                                /*
                                hidden = sigmoid((weight1*input) + bias1)
                                output = sigmoid((weight2*hidden)+ bias2)
                                */
                                /*
                                1️⃣ Forward:
                                h=σ(w1x+b1)h = \sigma(w_1 x + b_1)h=σ(w1​x+b1​)
                                y=σ(w2h+b2)y = \sigma(w_2 h + b_2)y=σ(w2​h+b2​)
                                */
                            }

                            $("#btn-clear-neural-val").on('click', function()
                            {
                                /* Cleaning up the data-table for new collection */

                                clearNeuralDataTable();
                            });

                            function train(){
                                let target1 = $("#target1-val").text();// output 1 target
                                let target2 = $("#target2-val").text();// output 2 target
                                let target3 = $("#target3-val").text();// output 3 target
                                let learning_rate = 10;

                                let input1 = parseFloat($("#input1-val").text());
                                let input2 = parseFloat($("#input2-val").text());
                                let input3 = parseFloat($("#input3-val").text());
                                
                                let weight1 = parseFloat($("#weight1-val").text());// input1 - hidden1 weight
                                let weight2 = parseFloat($("#weight2-val").text());// hidden1 - output1 weight
                                let weight3 = parseFloat($("#weight3-val").text());// input2 - hidden1 weight 
                                let weight4 = parseFloat($("#weight4-val").text());// input1 - hidden2 weight
                                let weight5 = parseFloat($("#weight5-val").text());// input2 - hidden2 weight
                                let weight6 = parseFloat($("#weight6-val").text());// hidden2 - output1 weight 
                                let weight7 = parseFloat($("#weight7-val").text());// hidden1 - output2 weight
                                let weight8 = parseFloat($("#weight8-val").text());// hidden2 - output2 weight 
                                let weight9 = parseFloat($("#weight9-val").text());// input3 - hidden1 weight
                                let weight10 = parseFloat($("#weight10-val").text());// input3 - hidden2 weight 
                                let weight11 = parseFloat($("#weight11-val").text());// input1 - hidden3 weight 
                                let weight12 = parseFloat($("#weight12-val").text());// input2 - hidden3 weight 
                                let weight13 = parseFloat($("#weight13-val").text());// input3 - hidden3 weight 
                                let weight14 = parseFloat($("#weight14-val").text());// hidden3 - output1 weight 
                                let weight15 = parseFloat($("#weight15-val").text());// hidden3 - output2 weight 
                                let weight16 = parseFloat($("#weight16-val").text());// hidden1 - output3 weight 
                                let weight17 = parseFloat($("#weight17-val").text());// hidden2 - output3 weight 
                                let weight18 = parseFloat($("#weight18-val").text());// hidden3 - output3 weight 

                                let bias1 = parseFloat($("#bias1-val").text());// hidden1 bias
                                let bias2 = parseFloat($("#bias2-val").text());// output1 bias
                                let bias3 = parseFloat($("#bias3-val").text());// hidden2 bias
                                let bias4 = parseFloat($("#bias4-val").text());// output2 bias
                                let bias5 = parseFloat($("#bias5-val").text());// hidden3 bias
                                let bias6 = parseFloat($("#bias6-val").text());// output3 bias

                                let hidden1 = parseFloat($("#hidden1-val").text());
                                let hidden2 = parseFloat($("#hidden2-val").text());
                                let hidden3 = parseFloat($("#hidden3-val").text());

                                let output1 = parseFloat($("#output1-val").text());
                                let output2 = parseFloat($("#output2-val").text());
                                let output3 = parseFloat($("#output3-val").text());

                                /*
                                output layer:
                                    error_output = target - output
                                    delta_output = error_output * (output * (1 - output))

                                hidden layer:
                                    error_hidden = delta_output * w2
                                    delta_hidden = error_hidden * (hidden * (1 - hidden))

                                weight and bias updates:
                                    w1 = w1 + learning_rate * delta_hidden * input
                                    b1 = b1 + learning_rate * delta_hidden
                                    
                                    w2 = w2 + learning_rate * delta_output * hidden
                                    b2 = b2 + learning_rate * delta_output
                                */

                                // output layer
                                let error_output1 = target1 - output1;
                                let delta_output1 = error_output1 * (output1 * (1-output1));
                                
                                let error_output2 = target2 - output2;
                                let delta_output2 = error_output2 * (output2 * (1-output2));

                                let error_output3 = target3 - output3;
                                let delta_output3 = error_output3 * (output3 * (1-output3));

                                // hidden layer 
                                // delta_h1 = (delta_y1 * w2 + delta_y2 * w3) * h1 * (1 - h1);
                                let error_hidden1 = delta_output1 * weight2;// weight 2 is hidden1-output 1 weight
                                let error2_hidden1 = delta_output2 * weight7;// weight 7 is hidden1-output 2 weight
                                let error3_hidden1 = delta_output3 * weight16;// weight 16 is hidden1-output 3 weight
                                let delta_hidden1 = (error_hidden1 + error2_hidden1) * hidden1 * (1-hidden1);

                                let error_hidden2 = delta_output2 * weight6;// weight 6 is hidden2-output 1 weight
                                let error2_hidden2 = delta_output2 * weight8;// weight 8 is hidden2-output 2 weight
                                let error3_hidden2 = delta_output3 * weight17;// weight 17 is hidden2-output 3 weight
                                let delta_hidden2 = (error_hidden2 + error2_hidden2) * hidden2 * (1-hidden2);
                                
                                let error_hidden3 = delta_output1 * weight14;// weight 14 is hidden3-output 1 weight
                                let error2_hidden3 = delta_output2 * weight15;// weight 15 is hidden3-output 2 weight
                                let error3_hidden3 = delta_output3 * weight18;// weight 18 is hidden3-output 3 weight
                                let delta_hidden3 = (error_hidden3 + error2_hidden3) * hidden3 * (1-hidden3);

                                // weight and bias updates
                                // input - hidden1
                                weight1 = weight1 + learning_rate * delta_hidden1 * input1;// input1
                                weight3 = weight3 + learning_rate * delta_hidden1 * input2;// input2
                                weight9 = weight9 + learning_rate * delta_hidden1 * input3;// input3
                                bias1 = bias1 + learning_rate * delta_hidden1;

                                // input - hidden2
                                weight4 = weight4 + learning_rate * delta_hidden2 * input1;// input1
                                weight5 = weight5 + learning_rate * delta_hidden2 * input2;// input2
                                weight10 = weight10 + learning_rate * delta_hidden2 * input3;// input3
                                bias3 = bias3 + learning_rate * delta_hidden2;

                                // input - hidden3
                                weight11 = weight11 + learning_rate * delta_hidden3 * input1;// input1
                                weight12 = weight12 + learning_rate * delta_hidden3 * input2;// input2
                                weight13 = weight13 + learning_rate * delta_hidden3 * input3;// input3
                                bias5 = bias5 + learning_rate * delta_hidden3;

                                // hidden - output1
                                weight2 = weight2 + learning_rate * delta_output1 * hidden1;// hidden1 - output1 connection
                                weight6 = weight6 + learning_rate * delta_output1 * hidden2;// hidden2 - output1 connection
                                weight14 = weight14 + learning_rate * delta_output1 * hidden3;// hidden3 - output1 connection
                                bias2 = bias2 + learning_rate * delta_output1;

                                // hidden - output2
                                weight7 = weight7 + learning_rate * delta_output2 * hidden1;// hidden1 - output2 connection
                                weight8 = weight8 + learning_rate * delta_output2 * hidden2;// hidden2 - output2 connection
                                weight15 = weight15 + learning_rate * delta_output2 * hidden3;// hidden3 - output2 connection
                                bias4 = bias4 + learning_rate * delta_output2;

                                // hidden - output3
                                weight16 = weight16 + learning_rate * delta_output3 * hidden1;// hidden1 - output3 connection
                                weight17 = weight17 + learning_rate * delta_output3 * hidden2;// hidden2 - output3 connection
                                weight18 = weight18 + learning_rate * delta_output3 * hidden3;// hidden3 - output3 connection
                                bias6 = bias6 + learning_rate * delta_output3;

                                // assignment of weights and biases to the labels
                                $("#weight1-val").text(weight1);
                                $("#weight3-val").text(weight3);
                                $("#weight9-val").text(weight9);
                                $("#bias1-val").text(bias1);

                                $("#weight4-val").text(weight4);
                                $("#weight5-val").text(weight5);
                                $("#weight10-val").text(weight10);
                                $("#bias3-val").text(bias3);
                                
                                $("#weight11-val").text(weight11);
                                $("#weight12-val").text(weight12);
                                $("#weight13-val").text(weight13);
                                $("#bias5-val").text(bias5);

                                $("#weight2-val").text(weight2);
                                $("#weight6-val").text(weight6);
                                $("#weight14-val").text(weight14);
                                $("#bias2-val").text(bias2);

                                $("#weight7-val").text(weight7);
                                $("#weight8-val").text(weight8);
                                $("#weight15-val").text(weight15);
                                $("#bias4-val").text(bias4);

                                $("#weight16-val").text(weight16);
                                $("#weight17-val").text(weight17);
                                $("#weight18-val").text(weight18);
                                $("#bias6-val").text(bias6);

                                // fire neural network once again
                                fire();
                            }

                            $("#btn-fire").on('click', function()
                            {
                                fire();
                            });

                            $("#btn-train").on('click', function()
                            {
                                train();
                            });

                            /* timer */
                            let count = 0;

                            /* pixel id initialization */
                            let pixId = -1;

                            // pixel id but x and y coordinates
                            let xId = parseInt($("#x-id").text());
                            let yId = parseInt($("#y-id").text());

                            /* Cycle Counter Initialization */
                            let cycleCounter = 1;
                            $("#cycle-counter").text(cycleCounter);

                            let animationId;

                            // Start timer
                            $("#btn-start-timer").on("click", function() {
                                async function step() {
                                    
                                    $(".tr-neural-data").css("background-color", "transparent");

                                    count++;
                                    $("#timer").text(count + " seconds");

                                    $("#x-id").text(xId);
                                    $("#y-id").text(yId);

                                    // pixel increment loop
                                    let maxPixId = parseInt($("#calculated-pixel-count").text())-1;
                                    if(pixId<maxPixId)
                                    {
                                        pixId++;
                                    }
                                    else
                                    {
                                        pixId=0;
                                        cycleCounter++;
                                    }
                                    $("#current-pixel-id-display").text(pixId + " / " + maxPixId);
                                    $("#row-" + pixId).css("background-color", "yellow");
                                    $("#cycle-counter").text(cycleCounter);

                                    tableToNetwork(pixId);
                                    if(cycleCounter<=1)
                                    {
                                        fire();
                                        train();
                                    }
                                    else if(cycleCounter>1)
                                    {
                                        train();
                                    }
                                    for(let trainIter=0; trainIter<10; trainIter++)train();
                                    networkToTable(pixId);

                                    // pixel coordinate display
                                    let uploadedResolutionWidth = parseInt($("#uploaded-image-resolution-width").text());
                                    let uploadedResolutionHeight = parseInt($("#uploaded-image-resolution-height").text());
                                    if(xId<uploadedResolutionWidth-1)
                                    {
                                        xId++;
                                    }
                                    else
                                    {
                                        xId=0;
                                        if(yId<uploadedResolutionHeight-1)
                                        {
                                            yId++;
                                        }
                                        else
                                        {
                                            yId=0;
                                        }
                                    }

                                    animationId = requestAnimationFrame(step); // smoother GPU timing
                                }
                                step();
                            });

                            // Stop timer
                            $("#btn-stop-timer").on("click", function() {
                                cancelAnimationFrame(animationId);
                            });

                            // Reset timer
                            $("#btn-reset-timer").on("click", function() {
                                count = 0;
                                $("#timer").text("0");
                            });
                            /* end of timer */

                        });
                    </script>
                    <!--
                    1️⃣ Forward:
                    h=σ(w1x+b1)h = \sigma(w_1 x + b_1)h=σ(w1​x+b1​)
                    y=σ(w2h+b2)y = \sigma(w_2 h + b_2)y=σ(w2​h+b2​)
                    
                    2️⃣ Backprop:
                    δout=(t−y)⋅y⋅(1−y)\delta_{out} = (t - y) \cdot y \cdot (1 - y)δout​=(t−y)⋅y⋅(1−y)
                    δhid=δout⋅w2⋅h⋅(1−h)\delta_{hid} = \delta_{out} \cdot w_2 \cdot h \cdot (1 - h)δhid​=δout​⋅w2​⋅h⋅(1−h)
                    
                    3️⃣ Adjust:
                    w2=w2+η⋅δout⋅hw_2 = w_2 + \eta \cdot \delta_{out} \cdot hw2​=w2​+η⋅δout​⋅h
                    b2=b2+η⋅δoutb_2 = b_2 + \eta \cdot \delta_{out}b2​=b2​+η⋅δout​
                    w1=w1+η⋅δhid⋅xw_1 = w_1 + \eta \cdot \delta_{hid} \cdot xw1​=w1​+η⋅δhid​⋅x
                    b1=b1+η⋅δhidb_1 = b_1 + \eta \cdot \delta_{hid}b1​=b1​+η⋅δhid​
                    -->
                </div>
            </div>
            
            <!-- Pixel Output Div-->
            <div class = "pixel-out">
                <h4>Output</h4>
                
                <canvas id="ai-canvas-display"></canvas>
            </div>
            
            <!-- Since this is 101 guess black or white, 101 means 1 input, 0 hidden, 1 output, meaning input-output only -->
            <!--Process:
            input got random 1 or 0
            weight will fire to the output, equation:
            output = sigmoid((weight*input) + bias)
            output got bias of random
            error = t - output, where t = target color inside the sigmoid range
            then new_weight = learningrate * delta * input 
            bias = learningrate * delta-->
        </div>

        <div class="data-table-container">
            <table id="data-table" class="transparent">
                <thead>
                    <tr>
                        <th colspan="1">ID</th>
                        <th colspan="3">Input</th>
                        <th colspan="3">Hidden</th>
                        <th colspan="3">Output</th>
                        <th colspan="3">Target</th>
                        <th colspan="18">Weight</th>
                        <th colspan="6">Bias</th>
                    </tr>
                    <tr>
                        <!-- <th>Document ID</th> -->
                        <th>Field ID</th>
                        <th>Input 1</th>
                        <th>Input 2</th>
                        <th>Input 3</th>
                        <th>Hidden 1</th>
                        <th>Hidden 2</th>
                        <th>Hidden 3</th>
                        <th>Output 1</th>
                        <th>Output 2</th>
                        <th>Output 3</th>
                        <th>Target 1</th>
                        <th>Target 2</th>
                        <th>Target 3</th>
                        <th>Weight 1</th>
                        <th>Weight 2</th>
                        <th>Weight 3</th>
                        <th>Weight 4</th>
                        <th>Weight 5</th>
                        <th>Weight 6</th>
                        <th>Weight 7</th>
                        <th>Weight 8</th>
                        <th>Weight 9</th>
                        <th>Weight 10</th>
                        <th>Weight 11</th>
                        <th>Weight 12</th>
                        <th>Weight 13</th>
                        <th>Weight 14</th>
                        <th>Weight 15</th>
                        <th>Weight 16</th>
                        <th>Weight 17</th>
                        <th>Weight 18</th>
                        <th>Bias 1</th>
                        <th>Bias 2</th>
                        <th>Bias 3</th>
                        <th>Bias 4</th>
                        <th>Bias 5</th>
                        <th>Bias 6</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
      
        <div class="panel-generate">
            <input id="input-upload" type="file" accept="image/*">
            <p>
                Uploaded Image Resolution: <span id="uploaded-image-resolution-width">0</span> x <span id="uploaded-image-resolution-height">0</span><br>
                Calculated Pixel Count: <span id="calculated-pixel-count">0</span><span>px</span>
            </p>
            <canvas id="canvas-display-image"></canvas>

            <script>

                $(function()
                {
                    $("#input-upload").on("change", function(event)
                    {
                        const file = event.target.files[0];
                        if (!file) return; // stop if no file selected

                        const reader = new FileReader();

                        reader.onload = function(e)
                        {
                            const img = new Image();
                            img.onload = function()
                            {
                                <!-- Make sure TensorFlow.js is loaded -->
                                clearNeuralDataTableDb();

                                const canvas = $("#canvas-display-image")[0];
                                const canvasAi = $("#ai-canvas-display")[0];
                                const ctx = canvas.getContext("2d");

                                // Set canvas size to match uploaded image
                                canvas.width = img.width;
                                canvas.height = img.height;
                                canvasAi.width = img.width;
                                canvasAi.height = img.height;

                                $("#uploaded-image-resolution-width").text(img.width);
                                $("#uploaded-image-resolution-height").text(img.height);

                                ctx.imageSmoothingEnabled = false;
                                ctx.clearRect(0, 0, canvas.width, canvas.height);
                                ctx.drawImage(img, 0, 0, img.width, img.height);

                                // Extract pixel data
                                const imageData = ctx.getImageData(0, 0, img.width, img.height);
                                const data = imageData.data; // flat RGBA array

                                const totalPixelCount = img.width * img.height;
                                $("#calculated-pixel-count").text(totalPixelCount);

                                // Convert flat RGBA array to Float32Array for RGB only and normalize
                                const floatData = new Float32Array(totalPixelCount * 3); // [R,G,B,R,G,B,...]
                                for (let i = 0, j = 0; i < data.length; i += 4, j += 3) {
                                    floatData[j] = data[i] / 256;     // R normalized
                                    floatData[j + 1] = data[i + 1] / 256; // G normalized
                                    floatData[j + 2] = data[i + 2] / 256; // B normalized
                                }

                                // Create TensorFlow.js tensor: shape [height, width, 3]
                                const inputTensor = tf.tensor3d(floatData, [img.height, img.width, 3]);

                                // OPTIONAL: Print tensor info
                                console.log("Tensor shape:", inputTensor.shape);
                                console.log("Tensor dtype:", inputTensor.dtype);

                                // Now feed tensor to GPU for any processing or neural calculations
                                // Example: just read back pixels from tensor and append neural table
                                tf.tidy(() => {
                                    // Flatten tensor to 1D array
                                    const tensorData = inputTensor.reshape([totalPixelCount, 3]).arraySync();

                                    for (let pxI = 0; pxI < totalPixelCount; pxI++) {
                                        const [r, g, b] = tensorData[pxI]; // normalized values 0-1

                                        const x = pxI % img.width;
                                        const y = Math.floor(pxI / img.width);

                                        console.clear();
                                        console.log(`Pixel (${x},${y}) -> R=${r}, G=${g}, B=${b}`);

                                        // Generate random neural input/weights/bias
                                        const input = Array.from({length: 3}, () => Math.random());
                                        const weight = Array.from({length: 19}, () => Math.random());
                                        const bias = Array.from({length: 7}, () => Math.random());
                                        const target = [r, g, b];

                                        appendNeuralDataRowDb(pxI, input, target, weight, bias);
                                    }
                                });
                            };
                            img.src = e.target.result;
                        }

                        reader.readAsDataURL(file);
                    });
                });
            </script>
        </div>
    </div>
</body>
</html>
