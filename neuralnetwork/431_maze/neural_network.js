$(function()
{
    
    let inputNeurons = [];
    let hiddenNeurons = [];
    let outputNeurons = [];

    // biases
    let biasH = []; // hidden layer biases
    let biasO = []; // output layer biases

    let weights_ih = []; // weights from input to hidden
    let weights_ho = []; // weights from hidden to output

    function sigmoid(x)
    {
        return 1 / (1 + Math.exp(-x));
    }

    function NeuralNetwork(inputNeuronsCount, hiddenNeuronsCount, outputNeuronsCount)
    {
        // input neurons value initialization
        for(let a = 0; a < inputNeuronsCount; a++)
        {
            inputNeurons.push(Math.random());
            $(".neural-values-text").append("Input " + (a+1) + ": <span id='input"+(a+1)+"-val'>" + inputNeurons[a] + "</span><br>");
        }

        $(".neural-values-text").append("<br>");

        // weights initialization from input to hidden
        for(let d = 0; d < hiddenNeuronsCount; d++)
        {
            weights_ih[d] = [];
            for(let e = 0; e < inputNeuronsCount; e++)
            {
                weights_ih[d][e] = Math.random();
                $(".neural-values-text").append("W H" + (d+1) + "_I" + (e+1) + ": <span id='weighth"+(d+1)+"i"+(e+1)+"-val'>" + weights_ih[d][e] + "</span><br>");
            }
            
            $(".neural-values-text").append("<br>");
        }

        $(".neural-values-text").append("<br>");
        
        // hidden layer biases initialization
        for(let bh = 0; bh < hiddenNeuronsCount; bh++)
        {
            biasH.push(Math.random());
            $(".neural-values-text").append("Bias H" + (bh+1) + ": <span id='biash"+(bh+1)+"-val'>" + biasH[bh] + "</span><br>");
        }
        $(".neural-values-text").append("<br>");
        // hidden neurons value initialization
        for(let b = 0; b < hiddenNeuronsCount; b++)
        {
            hiddenNeurons.push(0);// initial value 0
            $(".neural-values-text").append("Hidden " + (b+1) + ": <span id='hidden"+(b+1)+"-val'>" + hiddenNeurons[b] + "</span><br>");
        }

        $(".neural-values-text").append("<br>");

        // weights initialization from hidden to output
        for(let f = 0; f < outputNeuronsCount; f++)
        {
            weights_ho[f] = [];
            for(let g = 0; g < hiddenNeuronsCount; g++)
            {
                weights_ho[f][g] = Math.random();
                $(".neural-values-text").append("W O" + (f+1) + "_H" + (g+1) + ": <span id='weighto"+(f+1)+"h"+(g+1)+"-val'>" + weights_ho[f][g] + "</span><br>");
            }

            
            $(".neural-values-text").append("<br>");
        }

        $(".neural-values-text").append("<br>");

        // output layer biases initialization
        for(let bo = 0; bo < outputNeuronsCount; bo++)
        {
            biasO.push(Math.random());
            $(".neural-values-text").append("Bias O" + (bo+1) + ": <span id='biaso"+(bo+1)+"-val'>" + biasO[bo] + "</span><br>");
        }
        $(".neural-values-text").append("<br>");
        // output neurons value initialization
        for(let c = 0; c < outputNeuronsCount; c++)
        {
            outputNeurons.push(0);// initial value 0
            $(".neural-values-text").append("Output " + (c+1) + ": <span id='output"+(c+1)+"-val'>" + outputNeurons[c] + "</span><br>");
        }

    }
//weightinputhidden, biashidden, weighthiddenoutput, biasoutput, 
    function fire(inputNeurons, hiddenNeurons, outputNeurons)
    {
        console.log("input count: " + inputNeurons.length);
        console.log("hidden count: " + hiddenNeurons.length);
        console.log("output count: " + outputNeurons.length);

        // hidden layer firing
        for(let hiddenId=0;hiddenId<hiddenNeurons.length;hiddenId++)
        {
            hiddenNeurons[hiddenId] = 0; // for refreshing the container value to prevent capability of computing the previous value to the current real values of inputs, weights, and biases
            for(let inputId=0;inputId<inputNeurons.length;inputId++)
            {
                hiddenNeurons[hiddenId] += (weights_ih[hiddenId][inputId] * inputNeurons[inputId]);
            }
            hiddenNeurons[hiddenId] += biasH[hiddenId];
            hiddenNeurons[hiddenId] = sigmoid(hiddenNeurons[hiddenId]);
            $("#hidden"+(hiddenId+1)+"-val").text(hiddenNeurons[hiddenId]);
        }

        // output layer firing
        for(let outputId=0;outputId<outputNeurons.length;outputId++)
        {
            outputNeurons[outputId] = 0; // for refreshing the container value to prevent capability of computing the previous value to the current real values of hiddens, weights, and biases
            for(let hiddenId=0;hiddenId<hiddenNeurons.length;hiddenId++)
            {
                outputNeurons[outputId] += (weights_ho[outputId][hiddenId] * hiddenNeurons[hiddenId]);
            }
            outputNeurons[outputId] += biasO[outputId];
            outputNeurons[outputId] = sigmoid(outputNeurons[outputId]);
            $("#output"+(outputId+1)+"-val").text(outputNeurons[outputId]);
        }
    }
    // end of fire

    function train(inputNeurons, hiddenNeurons, outputNeurons)
    {
        // let target1 = $("#target1-val").text();// output 1 target
        // let target2 = $("#target2-val").text();// output 2 target
        // let target3 = $("#target3-val").text();// output 3 target
        // let learning_rate = 10;

        // let input1 = parseFloat($("#input1-val").text());
        // let input2 = parseFloat($("#input2-val").text());
        // let input3 = parseFloat($("#input3-val").text());
        
        // let weight1 = parseFloat($("#weight1-val").text());// input1 - hidden1 weight
        // let weight2 = parseFloat($("#weight2-val").text());// hidden1 - output1 weight
        // let weight3 = parseFloat($("#weight3-val").text());// input2 - hidden1 weight 
        // let weight4 = parseFloat($("#weight4-val").text());// input1 - hidden2 weight
        // let weight5 = parseFloat($("#weight5-val").text());// input2 - hidden2 weight
        // let weight6 = parseFloat($("#weight6-val").text());// hidden2 - output1 weight 
        // let weight7 = parseFloat($("#weight7-val").text());// hidden1 - output2 weight
        // let weight8 = parseFloat($("#weight8-val").text());// hidden2 - output2 weight 
        // let weight9 = parseFloat($("#weight9-val").text());// input3 - hidden1 weight
        // let weight10 = parseFloat($("#weight10-val").text());// input3 - hidden2 weight 
        // let weight11 = parseFloat($("#weight11-val").text());// input1 - hidden3 weight 
        // let weight12 = parseFloat($("#weight12-val").text());// input2 - hidden3 weight 
        // let weight13 = parseFloat($("#weight13-val").text());// input3 - hidden3 weight 
        // let weight14 = parseFloat($("#weight14-val").text());// hidden3 - output1 weight 
        // let weight15 = parseFloat($("#weight15-val").text());// hidden3 - output2 weight 
        // let weight16 = parseFloat($("#weight16-val").text());// hidden1 - output3 weight 
        // let weight17 = parseFloat($("#weight17-val").text());// hidden2 - output3 weight 
        // let weight18 = parseFloat($("#weight18-val").text());// hidden3 - output3 weight 

        // let bias1 = parseFloat($("#bias1-val").text());// hidden1 bias
        // let bias2 = parseFloat($("#bias2-val").text());// output1 bias
        // let bias3 = parseFloat($("#bias3-val").text());// hidden2 bias
        // let bias4 = parseFloat($("#bias4-val").text());// output2 bias
        // let bias5 = parseFloat($("#bias5-val").text());// hidden3 bias
        // let bias6 = parseFloat($("#bias6-val").text());// output3 bias

        // let hidden1 = parseFloat($("#hidden1-val").text());
        // let hidden2 = parseFloat($("#hidden2-val").text());
        // let hidden3 = parseFloat($("#hidden3-val").text());

        // let output1 = parseFloat($("#output1-val").text());
        // let output2 = parseFloat($("#output2-val").text());
        // let output3 = parseFloat($("#output3-val").text());

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
        // let error_output1 = target1 - output1;
        // let delta_output1 = error_output1 * (output1 * (1-output1));
        
        // let error_output2 = target2 - output2;
        // let delta_output2 = error_output2 * (output2 * (1-output2));

        // let error_output3 = target3 - output3;
        // let delta_output3 = error_output3 * (output3 * (1-output3));

        // hidden layer 
        // delta_h1 = (delta_y1 * w2 + delta_y2 * w3) * h1 * (1 - h1);
        // let error_hidden1 = delta_output1 * weight2;// weight 2 is hidden1-output 1 weight
        // let error2_hidden1 = delta_output2 * weight7;// weight 7 is hidden1-output 2 weight
        // let error3_hidden1 = delta_output3 * weight16;// weight 16 is hidden1-output 3 weight
        // let delta_hidden1 = (error_hidden1 + error2_hidden1) * hidden1 * (1-hidden1);

        // let error_hidden2 = delta_output2 * weight6;// weight 6 is hidden2-output 1 weight
        // let error2_hidden2 = delta_output2 * weight8;// weight 8 is hidden2-output 2 weight
        // let error3_hidden2 = delta_output3 * weight17;// weight 17 is hidden2-output 3 weight
        // let delta_hidden2 = (error_hidden2 + error2_hidden2) * hidden2 * (1-hidden2);
        
        // let error_hidden3 = delta_output1 * weight14;// weight 14 is hidden3-output 1 weight
        // let error2_hidden3 = delta_output2 * weight15;// weight 15 is hidden3-output 2 weight
        // let error3_hidden3 = delta_output3 * weight18;// weight 18 is hidden3-output 3 weight
        // let delta_hidden3 = (error_hidden3 + error2_hidden3) * hidden3 * (1-hidden3);

        // weight and bias updates
        // input - hidden1
        // weight1 = weight1 + learning_rate * delta_hidden1 * input1;// input1
        // weight3 = weight3 + learning_rate * delta_hidden1 * input2;// input2
        // weight9 = weight9 + learning_rate * delta_hidden1 * input3;// input3
        // bias1 = bias1 + learning_rate * delta_hidden1;

        // input - hidden2
        // weight4 = weight4 + learning_rate * delta_hidden2 * input1;// input1
        // weight5 = weight5 + learning_rate * delta_hidden2 * input2;// input2
        // weight10 = weight10 + learning_rate * delta_hidden2 * input3;// input3
        // bias3 = bias3 + learning_rate * delta_hidden2;

        // input - hidden3
        // weight11 = weight11 + learning_rate * delta_hidden3 * input1;// input1
        // weight12 = weight12 + learning_rate * delta_hidden3 * input2;// input2
        // weight13 = weight13 + learning_rate * delta_hidden3 * input3;// input3
        // bias5 = bias5 + learning_rate * delta_hidden3;

        // hidden - output1
        // weight2 = weight2 + learning_rate * delta_output1 * hidden1;// hidden1 - output1 connection
        // weight6 = weight6 + learning_rate * delta_output1 * hidden2;// hidden2 - output1 connection
        // weight14 = weight14 + learning_rate * delta_output1 * hidden3;// hidden3 - output1 connection
        // bias2 = bias2 + learning_rate * delta_output1;

        // hidden - output2
        // weight7 = weight7 + learning_rate * delta_output2 * hidden1;// hidden1 - output2 connection
        // weight8 = weight8 + learning_rate * delta_output2 * hidden2;// hidden2 - output2 connection
        // weight15 = weight15 + learning_rate * delta_output2 * hidden3;// hidden3 - output2 connection
        // bias4 = bias4 + learning_rate * delta_output2;

        // hidden - output3
        // weight16 = weight16 + learning_rate * delta_output3 * hidden1;// hidden1 - output3 connection
        // weight17 = weight17 + learning_rate * delta_output3 * hidden2;// hidden2 - output3 connection
        // weight18 = weight18 + learning_rate * delta_output3 * hidden3;// hidden3 - output3 connection
        // bias6 = bias6 + learning_rate * delta_output3;

        // assignment of weights and biases to the labels
        // $("#weight1-val").text(weight1);
        // $("#weight3-val").text(weight3);
        // $("#weight9-val").text(weight9);
        // $("#bias1-val").text(bias1);

        // $("#weight4-val").text(weight4);
        // $("#weight5-val").text(weight5);
        // $("#weight10-val").text(weight10);
        // $("#bias3-val").text(bias3);
        
        // $("#weight11-val").text(weight11);
        // $("#weight12-val").text(weight12);
        // $("#weight13-val").text(weight13);
        // $("#bias5-val").text(bias5);

        // $("#weight2-val").text(weight2);
        // $("#weight6-val").text(weight6);
        // $("#weight14-val").text(weight14);
        // $("#bias2-val").text(bias2);

        // $("#weight7-val").text(weight7);
        // $("#weight8-val").text(weight8);
        // $("#weight15-val").text(weight15);
        // $("#bias4-val").text(bias4);

        // $("#weight16-val").text(weight16);
        // $("#weight17-val").text(weight17);
        // $("#weight18-val").text(weight18);
        // $("#bias6-val").text(bias6);

        // fire neural network once again
        // fire();
    }
    // end of train

    $("#btn-fire").on('click', function()
    {
        fire(inputNeurons, hiddenNeurons, outputNeurons);
    });

    // function train()
    // {

    // }


    /* Execution */

    // first, a 4-3-1 maze neural network project
    // 4 input neurons - for sides of direction: up, right, down, left 
    // 3 hidden neurons - for decision making if to turn left, right or go forward
    // 1 output neuron - for the final decision
    NeuralNetwork(4, 1, 1); // 4 input neurons, 3 hidden neurons, 1 output  
});