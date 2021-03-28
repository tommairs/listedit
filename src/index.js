import Quill from "quill";
import BlotFormatter from "quill-blot-formatter";

Quill.register("modules/blotFormatter", BlotFormatter);

const quill = new Quill("#quill-container", {
  theme: "snow",
  scrollingContainer: "#scrolling-container",
  modules: {
    blotFormatter: {}
  }
});

