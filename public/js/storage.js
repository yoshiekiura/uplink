class Storage {
    constructor(props) {
        this.storageName = props;
        this.localStorage = localStorage;
    }
    set = (value) => {
        let type = typeof value;
        let toSet = type == "string" ? value : JSON.stringify(value);
        localStorage.setItem(this.storageName, toSet);
        return this.get(this.storageName);
    }
    isJSON = string => {
        try {
            JSON.parse(string);
        }catch (e) {
            return false;
        }
        return true;
    }
    get = () => {
        let data = localStorage.getItem(this.storageName);
        return this.isJSON(data) ? JSON.parse(data) : data;
    }
    remove = () => {
        localStorage.remove(this.storageName);
        return this.get(this.storageName);
    }
    clear = () => {
        localStorage.clear();
    }
}