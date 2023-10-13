import metadata from "./block.json";
import Edit from "./edit";
import Save from "./save";
import "./style.scss";
import deprecated from "./deprecated";

const PersonCard = {
  ...metadata,
  edit: Edit,
  save: Save,
  deprecated,
};

export default PersonCard;
