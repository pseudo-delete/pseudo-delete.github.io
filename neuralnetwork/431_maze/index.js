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
        for(let a = 0; a < inputNeurons; a++)
        {
            inputNeurons.push(Math.random());
        }

        for(let b = 0; b < hiddenNeurons; b++)
        {
            hiddenNeurons.push(Math.random());
        }

        for(let c = 0; c < outputNeurons; c++)
        {
            outputNeurons.push(Math.random());
        }


    }

    // first, a 4-3-1 maze neural network project
    // 4 input neurons - for sides of direction: up, right, down, left 
    // 3 hidden neurons - for decision making if to turn left, right or go forward
    // 1 output neuron - for the final decision
    var nn = new NeuralNetwork(4, 3, 1); // 4 input neurons, 3 hidden neurons,  
    // 1 output neuron
    console.log(nn);
});