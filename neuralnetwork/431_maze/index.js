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
        }

        // hidden neurons value initialization
        // hidden layer biases initialization
        for(let b = 0; b < hiddenNeuronsCount; b++)
        {
            hiddenNeurons.push(0);// initial value 0
            biasH.push(Math.random());
        }

        // output neurons value initialization
        // output layer biases initialization
        for(let c = 0; c < outputNeuronsCount; c++)
        {
            outputNeurons.push(0);// initial value 0
            biasO.push(Math.random());
        }

        // weights initialization from input to hidden
        for(let d = 0; d < hiddenNeuronsCount; d++)
        {
            weights_ih[d] = [];
            for(let e = 0; e < inputNeuronsCount; e++)
            {
                weights_ih[d][e] = Math.random();
            }
        }

        // weights initialization from hidden to output
        for(let f = 0; f < outputNeuronsCount; f++)
        {
            weights_ho[f] = [];
            for(let g = 0; g < hiddenNeuronsCount; g++)
            {
                weights_ho[f][g] = Math.random();
            }
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