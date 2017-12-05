/**
 * Created by huangjianwu on 2017/11/30.
 */
openChat = function () {
    //window.location.href="http://www.hjw123.xin:55151/";
    var newFrame = document.getElementsByTagName("iframe");

    newFrame.src ="http://www.hjw123.xin:55151/";
    newFrame.frameBorder = 0;//FF、IE隐藏边框有效
    newFrame.width = "400px";
    newFrame.height = "400px";
    newFrame.scrolling = "no";
    document.body.appendChild(newFrame);
}