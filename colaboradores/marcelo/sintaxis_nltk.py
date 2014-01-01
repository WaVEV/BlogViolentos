import nltk	
import sys 

sentence = sys.argv[1]



from nltk.corpus import cess_esp as cess
from nltk import UnigramTagger as ut
cess_sents = cess.tagged_sents()
uni_tag = ut(cess_sents)

print uni_tag.tag(sentence.split(" "))





