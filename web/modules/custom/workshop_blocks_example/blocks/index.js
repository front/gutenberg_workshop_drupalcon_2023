import { registerBlockType } from "@wordpress/blocks";
import { myBlock } from "./my-block";

registerBlockType("workshop-blocks-example/my-block", myBlock);
