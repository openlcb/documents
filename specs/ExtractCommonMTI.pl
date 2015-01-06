#!/opt/local/bin/perl
use OpenOffice::OODoc;
use Getopt::Long;

my $dir ;
my $headers = 0;
GetOptions ("dir:s"     => \$dir,       # change working directory
            "headers"   => \$headers)   # print column headings from each table in the docs
or die("Error in command line arguments\n");

if( defined($dir)){
    chdir $dir;
}

my @files = glob("*S.odt");

print "\"Source File\",\"Name\",\"Dest ID\",\"Event ID\",\"Simple Node\",\"Common MTI\",\"Data Content\",\"Comments\"\n";

foreach my $file (@files)
{
    my $doc = odfDocument(file => $file);
    my $t=0;
    while (my $table = $doc->getTable($t, 'normalize'))
    {
        my ($rows, $columns) = $doc->getTableSize($table);
        if(($rows > 1 ) && ($columns >= 4 ))
        {
            my $CommonMTIFound = 0;
            
            for( my $i = 0; $i < $columns; $i++ )
            {
                if( index($doc->getCellValue($table, 0, $i), "MTI") != -1)
                {
                    $CommonMTIFound = 1;
                }
            }
            if( $CommonMTIFound )
            {
                my $firstrow = 1;
                if($headers){
                    $firstrow = 0;
                }
                
                for (my $row = $firstrow; $row < $rows; $row++)
                {
                    print "\"", $file, "\",";
                 
                    for (my $column = 0; $column < $columns; $column++)
                    {
                        if( ( $columns == 5 ) && ($column == 3) ) {
                            print "\"\"," ;
                        }
                        
                        print "\"", $doc->getCellValue($table, $row, $column ), "\"," ;
                    }
                    print "\n";
                }
            }
        }
        
        $t++;
    }
}

