/*
 SyntaxHighlighter
 http://alexgorbatchev.com/

 SyntaxHighlighter is donationware. If you are using it, please donate.
 http://alexgorbatchev.com/wiki/SyntaxHighlighter:Donate

 @version
 2.1.382 (June 24 2010)

 @copyright
 Copyright (C) 2004-2009 Alex Gorbatchev.

 @license
 This file is part of SyntaxHighlighter.

 SyntaxHighlighter is free software: you can redistribute it and/or modify
 it under the terms of the GNU Lesser General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 SyntaxHighlighter is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with SyntaxHighlighter.  If not, see <http://www.gnu.org/copyleft/lesser.html>.
*/
SyntaxHighlighter.brushes.Groovy=function(){this.regexList=[{regex:SyntaxHighlighter.regexLib.singleLineCComments,css:"comments"},{regex:SyntaxHighlighter.regexLib.multiLineCComments,css:"comments"},{regex:SyntaxHighlighter.regexLib.doubleQuotedString,css:"string"},{regex:SyntaxHighlighter.regexLib.singleQuotedString,css:"string"},{regex:/""".*"""/g,css:"string"},{regex:RegExp("\\b([\\d]+(\\.[\\d]+)?|0x[a-f0-9]+)\\b","gi"),css:"value"},{regex:RegExp(this.getKeywords("as assert break case catch class continue def default do else extends finally if in implements import instanceof interface new package property return switch throw throws try while public protected private static"),
"gm"),css:"keyword"},{regex:RegExp(this.getKeywords("void boolean byte char short int long float double"),"gm"),css:"color1"},{regex:RegExp(this.getKeywords("null"),"gm"),css:"constants"},{regex:RegExp(this.getKeywords("allProperties count get size collect each eachProperty eachPropertyName eachWithIndex find findAll findIndexOf grep inject max min reverseEach sort asImmutable asSynchronized flatten intersect join pop reverse subMap toList padRight padLeft contains eachMatch toCharacter toLong toUrl tokenize eachFile eachFileRecurse eachB yte eachLine readBytes readLine getText splitEachLine withReader append encodeBase64 decodeBase64 filterLine transformChar transformLine withOutputStream withPrintWriter withStream withStreams withWriter withWriterAppend write writeLine dump inspect invokeMethod print println step times upto use waitForOrKill getText"),
"gm"),css:"functions"}];this.forHtmlScript(SyntaxHighlighter.regexLib.aspScriptTags)};SyntaxHighlighter.brushes.Groovy.prototype=new SyntaxHighlighter.Highlighter;SyntaxHighlighter.brushes.Groovy.aliases=["groovy"];
