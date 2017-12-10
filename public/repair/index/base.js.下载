/*
 * 提供一些基础方法 与语言 结构相关性较大 与业务逻辑相关性较小 
 */

/*
 * 命名空间
 */
var IU = {};
IU.namespace = function(str) {
	var arr = str.split("."), o = IU;
	for (i = (arr[0] == "IU") ? 1 : 0; i < arr.length; i++) {
		o[arr[i]] = o[arr[i]] || {};
		o = o[arr[i]];
	}
}


/*
## Usage
(function() {
	IU.namespace('IU.somename');
})()
*/
