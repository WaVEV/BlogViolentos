# coding=utf8
from pattern.es import parse
import sys
import codecs

sentence = sys.argv[1]

s = parse(sentence)

print(codecs.encode(s,'utf8'))





