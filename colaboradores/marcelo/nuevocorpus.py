from glob import glob
from codecs import open, BOM_UTF8 


  
def wikicorpus(words=1000000, start=0):
    s = [[]]
    i = 0
    for f in glob("tagged.es/*")[start:]:
        for line in open(f, encoding="latin-1"):
            if line == "\n" or line.startswith((
              "<doc", "</doc>", "ENDOFARTICLE", "REDIRECT",
              "Acontecimientos", 
              "Fallecimientos", 
              "Nacimientos")):
                continue
            w, lemma, tag, x = line.split(" ")
            if tag.startswith("Fp"):
                tag = tag[:3]
            elif tag.startswith("V"):  # VMIP3P0 => VMI
                tag = tag[:3]
            elif tag.startswith("NC"): # NCMS000 => NCS
                tag = tag[:2] + tag[3]
            else:
                tag = tag[:2]
            for w in w.split("_"): # Puerto_Rico
                s[-1].append((w, tag)); i+=1
            if tag == "Fp" and w == ".":
                s.append([])
            if i >= words:
                return s[:-1]


