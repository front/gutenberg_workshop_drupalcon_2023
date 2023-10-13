import { registerBlockStyle, registerBlockType } from "@wordpress/blocks";
import { myBlock } from "./my-block";
import PersonCard from "./person-card";

registerBlockType("workshop-blocks-example/my-block", myBlock);
registerBlockType("workshop-blocks-example/person-card", PersonCard);

// Usually defined at theme level
// registerBlockStyle('workshop-blocks-example/person-card', {
//   name: 'image-right',
//   label: 'Image right'
// });
