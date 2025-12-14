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

        // hidden layer 
        for(let hiddenId=0;hiddenId<hidden.length;hiddenId++)
        {
            for(let inputId=0;inputId<input.length;inputId++)
            {
                hiddenNeurons[hiddenId] += (weights_ih[hiddenId][inputId] * inputNeurons[inputId]);
            }
            hiddenNeurons[hiddenId] += biasH[hiddenId];
            hiddenNeurons[hiddenId] = sigmoid(hiddenNeurons[hiddenId]);
            $("#hidden"+(hiddenId+1)+"-val").text(hiddenNeurons[hiddenId]);
        }

        // for(let hiddenId=0;hiddenId<hidden.length;hiddenId++)
        // {
        //     for(let inputId=0;inputId<input.length;inputId++)
        //     {
        //         /* loop simulation:
        //         hidden id will be slower, input id will be faster

        //         for weight ih:

        //         - weight i1h1
        //         - weight i2h1
        //         - weight i3h1
        //         - weight 14h1

        //         then,
        //         - weight i1h2
        //         - weight i2h2
        //         - weight i3h2
        //         - weight i4h2

        //         then,
        //         - weight i1h3
        //         - weight i2h3
        //         - weight i3h3
        //         - weight i4h3

        //         hidden1 = sigmoid((weight1*input1) + (weight3*input2) + (weight9*input3) + bias1);
                
        //         */
        //     }
        // }

        // output layer firing
        // for(let outputId=0;outputId<output.length;outputId++)
        // {
        //     for(let hiddenId=0;hiddenId<hidden.length;hiddenId++)
        //     {
        //         /* loop simulation:
        //         output id will be slower, hidden id will be faster
        //         */
        //     }
        // }
    }

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
    NeuralNetwork(1, 1, 1); // 4 input neurons, 3 hidden neurons, 1 output  
});