<?php

/*
 * 本脚本适用场景是已知一个正确的配置文件dest，和一个待校验的目标文件source，
 * 检查正确配置文件内的每个模块，比如mysql，redis等是否和待验证文件一致
 * 目前可以匹配每一个模块是否存在，以及模块内部每个环境是否正确
 * author：yuwenxiang
 * date：2020/6/7
 */

function KMP_Match(&$source,&$dest,&$next) //KMP函数，匹配字符串  source==>文本，dest==>待匹配字符串
{
    $i=0;   //i是文本的指针
    $j=0;   //j是待匹配字符串的指针
    $sLen=strlen($source);  //文本字符串的长度
    $dLen=strlen($dest);    //待匹配字符串的长度
    while( $i<$sLen && $j<$dLen )
    {
        if ( $j==-1 || $source[$i] == $dest[$j]) //j==-1 或者 目标字符串和文本对应的字符相等，
        {
            $i++;
            $j++;
        }
        else
        {
            $j= $next[$j];  //使用next数组

            /* 如果j!=-1，并且 匹配的字符串不相等，则将j回退到next[j]的位置
                                       i
                     S:  ABxxxxxxxxxxABC       $source[$i] != $dest[$j]时，将j移动到next[j]的位置，也就是2
                     P:  ABxxxxxxxxxxABD
                                       j
                            ||
                            ||
                            \/
                                      i
                     S: ABxxxxxxxxxxABC
                     P:             ABxxxxxxxxxxABD  j移动到下标为2的位置，去和文本比较
                                      j
             */
        }
    }

    if ( $j == $dLen)
    {

        return $i - $j; //返回目标字符串在文本内匹配到的字符串的起始位置

    }
    else

        return -1; //没匹配到就返回-1

}

function getNext(&$dest,&$next) //得到next数组
{
   $dLen=strlen($dest);
   $next[0]=-1;  //next[0]定义为-1
   $k=-1;        //前缀指针
   $j=0;         //后缀指针
    while ($j < $dLen - 1)
    {
        //p[k]表示前缀，p[j]表示后缀
        if ($k == -1 || $dest[$j] == $dest[$k])
        {
            $k++;
            $j++;
            $next[$j] = $k;
        }
        else
        {
            $k = $next[$k];
            /*$k在dLen[0]~dLen[$k-1]移动到最大前后缀匹配位置

                k        j
            ADADCxxxxADADD     $dest[$j] != $dest[$k];
              k          j
            ADADCxxxxADADD     $k = $next[$k]; 在next[$k]中寻找符合$dest[$j] == $dest[$k]的，一直递归

            */
        }
    }

}

function trim_all($str)  //去除空格的自定义函数
{
    $old=array(" ","  ","\t","\r"); //想要被替换的符号
    $new=array("","","","");        //将上述符号都替换为""
    return str_replace( $old,$new,$str); //使用str_replace函数，将$str内的空格去掉
}

    Global $next;
    $next= array(); //next数组

    //操作标准配置文件
    $dest = file_get_contents("dest"); //使用 file_get_contents 函数将待验证文件转化为字符串
    $dest_trim = trim_all($dest);    //去除空格，*@这里一定要记得将trim_all赋给一个变量@*
    //var_dump($dest_trim);
    //1.分割字符串，提取出[tianji]xxxxxx | [mysql]xxxx | [yyy]xxxxx .....
    //2.逐个匹配
    //3.匹配正确，输出对应模块的正确提示，反之亦然
    //

    $dest_trim_explode = explode ( "[" , $dest_trim  ); //将待验证文件中的每个待验证模块分割开来,输出是数组
    //var_dump($dest_trim_explode);

    //操作待验证配置文件
    $source=file_get_contents("source");      //文本文件转化为字符串
    $source_trim = trim_all($source);    //去除空格
    //var_dump($source_trim);             //var_dump($source);

    $source_trim_explode = explode ( "[" , $source_trim  ); //将待验证文件中的每个待验证模块分割开来,输出是数组
    //var_dump($source_trim_explode);

//    echo "===================\n";

    //将数组元素逐个进行匹配！！！！！！！
    foreach($dest_trim_explode as $dest_mod)     //循环每个模块，逐一匹配
    {
        $dest_mod_explode = explode("\n",$dest_mod);  //输出为 "mysql]" "host:"127.0.0.1""
        //var_dump($dest_mod_explode);
        $j = count($dest_mod_explode )-1; //模块内数组长度
        $MOD_NAME = substr($dest_mod_explode[0],0,-1);
        //var_dump($MOD_NAME);
        //var_dump($dest_mod_explode);
        if ($MOD_NAME == NULL) //去除掉第一条匹配
        {
            continue;
        }

        //1.匹配配置文件的模块名称
        getNext($MOD_NAME, $next);  //先找到next数组

        $i = count($source_trim_explode); //数组长度

        foreach ( $source_trim_explode as $key => $source_mod  ) {
            //echo"xxxxxxxxxxxxx\n";

            var_dump($source_mod);

            //print($dest_mod_explode[0]);
            //print("\n");
            $source_mod_all = '['.$source_mod; //合并字符串
            //var_dump($source_mod_all);
            //var_dump($dest_mod_explode[0]);
            //echo"xxxxxxxxxxxxx\n";
            $mod_loc_key = KMP_Match($source_mod_all, $MOD_NAME, $next); //用KMP算法寻找在文本内被匹配的字符串的指针位置
            //var_dump(KMP_Match($source_mod, $dest_mod_explode[0], $next)); //输出目标字符串在文本内匹配到的字符串的起始位置 int(x);


            //判断模块是否存在，可以优化
            if ($mod_loc_key < 0)
            {
                echo "FINDING... \n";
            }
            else {
               if($source_mod_all[$mod_loc_key-1] == '[' && $source_mod_all[strlen($MOD_NAME) + $mod_loc_key] == ']') //判断模块是否完全匹配
              {
                    print("$MOD_NAME MODULE EXIST \n "); //模块存在，输出相应语句
                    $a = 1;
                    while($dest_mod_explode[$a]<=$j) {

                        getNext($dest_mod_explode[$a], $next);  //先找到next数组
                        $mod_loc_value = KMP_Match($source_mod_all, $dest_mod_explode[$a], $next); //用KMP算法寻找在文本内被匹配的字符串的指针位置
                        if ($mod_loc_value > 0) {
                            if ($source_mod_all[$mod_loc_value - 1] == '#') {  //如果改地址被注释掉的情况，即#host:"127.0.0.1"
                                print("=======THE DEV YOU FIND IS NOT BE USED=======\n\n");
                            } else {//在当前模块内继续匹配
                                printf("$MOD_NAME DEV MATCHES \n\n"); // 模块内地址匹配正确
                                $a++;
                            }
                        } else
                            printf("$MOD_NAME DEV DOES NOT MATCH \n\n"); // 模块内地址匹配不正确
                        break;
                    }
              }
            }
            if($i == $key + 1)  //匹配到source文件最后一个模块也没匹配到对应的模块，则不存在对应模块
                echo "================ERROR!$MOD_NAME DOES NOT EXIST==============\n";
        }
    }
