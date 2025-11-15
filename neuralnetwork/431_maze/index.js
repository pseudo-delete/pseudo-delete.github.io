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
            $(".neural-values-text").append("Input Neuron " + (a+1) + ": <span id='input"+(a+1)+"-val'>" + inputNeurons[a] + "</span><br>");
        }

        // weights initialization from input to hidden
        for(let d = 0; d < hiddenNeuronsCount; d++)
        {
            weights_ih[d] = [];
            for(let e = 0; e < inputNeuronsCount; e++)
            {
                weights_ih[d][e] = Math.random();
                $(".neural-values-text").append("Weight input" + (d+1) + "_hidden" + (e+1) + ": <span id='weighti"+(d+1)+"h"+(e+1)+"-val'>" + weights_ih[d][e] + "</span><br>");
            }
        }

        // hidden neurons value initialization
        // hidden layer biases initialization
        for(let b = 0; b < hiddenNeuronsCount; b++)
        {
            hiddenNeurons.push(0);// initial value 0
            $(".neural-values-text").append("Hidden Neuron " + (b+1) + ": <span id='hidden"+(b+1)+"-val'>" + hiddenNeurons[b] + "</span><br>");

            biasH.push(Math.random());
            $(".neural-values-text").append("Bias Hidden " + (b+1) + ": <span id='biash"+(b+1)+"-val'>" + biasH[b] + "</span><br>");
        }

        // weights initialization from hidden to output
        for(let f = 0; f < outputNeuronsCount; f++)
        {
            weights_ho[f] = [];
            for(let g = 0; g < hiddenNeuronsCount; g++)
            {
                weights_ho[f][g] = Math.random();
                $(".neural-values-text").append("Weight hidden" + (f+1) + "output" + (g+1) + ": <span id='weighth"+(f+1)+"o"+(g+1)+"-val'>" + weights_ho[f][g] + "</span><br>");
            }
        }

        // output neurons value initialization
        // output layer biases initialization
        for(let c = 0; c < outputNeuronsCount; c++)
        {
            outputNeurons.push(0);// initial value 0
            $(".neural-values-text").append("Output Neuron " + (c+1) + ": <span id='output"+(c+1)+"-val'>" + outputNeurons[c] + "</span><br>");

            biasO.push(Math.random());
            $(".neural-values-text").append("Bias Output " + (c+1) + ": <span id='biaso"+(c+1)+"-val'>" + biasO[c] + "</span><br>");
        }

    }

    // first, a 4-3-1 maze neural network project
    // 4 input neurons - for sides of direction: up, right, down, left 
    // 3 hidden neurons - for decision making if to turn left, right or go forward
    // 1 output neuron - for the final decision
    NeuralNetwork(4, 3, 1); // 4 input neurons, 3 hidden neurons,  
    // 1 output neuron
    console.log(nn);
});