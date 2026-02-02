import { getBoardState } from "./board_state.js";

let neuron = Array.from({length:3}, () =>Array(9).fill(0));
let weight = Array.from({length: 2}, () => Array.from({length: 9}, () => Array(9).fill(0)));
let bias = Array.from({length:2}, () => Array(9).fill(0));

export function initializeNewNeuralState()
{
    let input = getBoardState();
    neuron[0] = input.slice(); // Input layer
    alert(neuron[0]);
}