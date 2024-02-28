import { default as Glitch } from "/assets/js/modules/glitch/Glitch.mjs";

const canvas = document.querySelector("canvas");
canvas._glitch = new Glitch(canvas);